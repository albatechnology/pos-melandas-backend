<?php

namespace App\Models;

use App\Classes\CartItem;
use App\Enums\UserType;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\CustomCastEnums;
use App\Traits\IsTenanted;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements Tenanted
{
    use HasApiTokens, SoftDeletes, Notifiable, Auditable, HasFactory, NodeTrait, IsTenanted, CastsEnums, CustomCastEnums;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'type',
        'supervisor_type_id',
        'supervisor_id',
        'company_id',
        'channel_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id'    => 'integer',
        'supervisor_id' => 'integer',
        'channel_id'    => 'integer',
    ];

    protected $enum_casts = [
        'type' => UserType::class,
    ];

    /**
     * required setup for nested tree package
     * @return string
     */
    public function getParentIdName(): string
    {
        return 'supervisor_id';
    }

    /**
     * Only applies to supervisor
     */
    public function getAllChildrenSales()
    {
        if (!$this->is_supervisor) {
            throw new Exception(sprintf(
                'getAllChildrenSales() can only be called from a supervisor! Called by user id %s',
                $this->id
            ));
        }

        return User::whereDescendantOf($this->id)->whereIsSales()->get();
    }

    //region Scopes

    public function scopeWhereSupervisorId($query, $ids)
    {
        return $query->whereIn('supervisor_id', explode(',', $ids));
    }

    public function scopeWhereSupervisorTypeId($query, $ids)
    {
        return $query->whereIn('supervisor_type_id', explode(',', $ids));
    }

    public function scopeWhereIsSupervisor($query)
    {
        return $query->where('type', UserType::SUPERVISOR);
    }

    public function scopeWhereIsSales($query)
    {
        return $query->where('type', UserType::SALES);
    }

    /**
     * Override tenanted scope.
     *
     * @param $query
     * @return mixed
     */
    public function scopeTenanted($query): mixed
    {
        $hasActiveChannel = tenancy()->getActiveTenant();
        $hasActiveCompany = tenancy()->getActiveCompany();
        $isAdmin          = tenancy()->getUser()->is_admin;

        if (!$hasActiveChannel && $isAdmin) {
            return $query;
        }

        if ($hasActiveChannel) {
            return $query->whereHas('channels', function ($query) {
                $query->where('id', tenancy()->getActiveTenant()->id);
            });
        }

        if ($hasActiveCompany) {
            if ($isAdmin) {
                // lets admin see all channels in a company
                return $query->whereHas('channels', function ($query) use ($hasActiveCompany) {
                    $query->whereIn('company_id', $hasActiveCompany->id);
                });
            } else {
                // lets user see all resource available to the user's channel within a company
                return $query->whereHas('channels', function ($query) use ($hasActiveCompany) {
                    $query->whereIn('id', tenancy()->getTenants()->pluck('id'))
                          ->whereIn('company_id', $hasActiveCompany->id);
                });
            }
        } else {
            if ($isAdmin) {
                // lets admin see all
                return $query;
            } else {
                // lets user see all resource available to the user's channel
                return $query->whereHas('channels', function ($query) {
                    $query->whereIn('id', tenancy()->getTenants()->pluck('id'));
                });
            }
        }
    }


    //endregion

    //region Attributes

    public function setSupervisorIdAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }

    public function getIsSalesAttribute(): bool
    {
        return $this->type->is(UserType::SALES);
    }

    public function getIsSupervisorAttribute(): bool
    {
        return $this->type->is(UserType::SUPERVISOR);
    }

    public function getIsAdminAttribute(): bool
    {
        if ($this->is_director) return true;
        return $this->roles()->where('id', 1)->exists();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getIsDevAttribute(): bool
    {
        return $this->is_admin;
    }

    public function getIsDirectorAttribute(): bool
    {
        return $this->type->is(UserType::DIRECTOR);
    }

    public function getEmailVerifiedAtAttribute($value): ?string
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::parse($value) : null;
    }

    //endregion

    // region Relationships

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function supervisorType(): BelongsTo
    {
        return $this->belongsTo(SupervisorType::class, 'supervisor_type_id');
    }

    public function supervisor_type(): BelongsTo
    {
        return $this->belongsTo(SupervisorType::class, 'supervisor_type_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function userActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'id');
    }

    public function userActivityComments(): HasMany
    {
        return $this->hasMany(ActivityComment::class, 'user_id', 'id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'user_id', 'id');
    }

    public function userOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function approvedByPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'approved_by_id', 'id');
    }

    public function fulfilledByShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'fulfilled_by_id', 'id');
    }

    public function fulfilledByInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'fulfilled_by_id', 'id');
    }

    public function supervisorUsers(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id', 'id');
    }

    public function requestedByStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'requested_by_id', 'id');
    }

    public function approvedByStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'approved_by_id', 'id');
    }

    public function channelsPivot(): HasMany
    {
        return $this->hasMany(ChannelUser::class);
    }

    public function qaTopics(): HasMany
    {
        return $this->hasMany(QaTopic::class, 'creator_id', 'id');
    }

    public function userUserAlerts(): BelongsToMany
    {
        return $this->belongsToMany(UserAlert::class);
    }

    /**
     * Default channel selected
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    // endregion

    public function syncCart(CartItem $cartItem): Cart
    {

        $cart = Cart::updateOrCreate(
            ['user_id' => $this->id],
            [
                'items' => $cartItem,
            ]
        );

        $cart->updatePricesFromItemLine();
        $cart->save();

        return $cart;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * A user can access user data about itself, its supervisor (direct parent),
     * and all its children/grand children nodes (all depths)
     * @param User $user the user that current user is attempting to view
     * @return bool
     */
    public function canView(User $user): bool
    {
        if ($this->id === $user->id) return true;

        if ($this->supervisor_id === $user->id) return true;

        if ($this->children->contains(fn(User $u) => $u->id === $user->id)) return true;

        return false;
    }

    /**
     * If this user does not have a default channel_id, auto set
     * it only if this user only have access to one channel.
     *
     * @return bool return true if this user now have a default company_id
     */
    public function setDefaultChannel(): bool
    {
        if ($this->channel_id) return true;

        $channels = $this->channels;
        if ($channels->count() == 1) {
            $this->update(['channel_id' => $channels->first()->id]);
            return true;
        }

        return false;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array $abilities
     * @return NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name'             => $name,
            'token'            => hash('sha256', $plainTextToken = Str::random(40)),
            'plain_text_token' => $plainTextToken,
            'abilities'        => $abilities,
        ]);

        return new NewAccessToken($token, $token->id . '|' . $plainTextToken);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
