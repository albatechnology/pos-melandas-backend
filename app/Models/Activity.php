<?php

namespace App\Models;

use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperActivity
 */
class Activity extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable, IsTenanted;

    use IsTenanted {
        IsTenanted::scopeTenanted as protected defaultScopeTenanted;
    }

    const STATUS_SELECT = [
        'hot'    => 'Hot',
        'warm'   => 'Warm',
        'cold'   => 'Cold',
        'closed' => 'Closed',
    ];

    const FOLLOW_UP_METHOD_SELECT = [
        'phone'    => 'Phone Call',
        'whatsapp' => 'Whatsapp',
        'meeting'  => 'Meeting',
        'others'   => 'Others',
    ];

    public $table = 'activities';

    protected $dates = [
        'follow_up_datetime',
        'created_at',
        'updated_at',
        'deleted_at',
        'reminder_datetime',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'user_id'                    => 'integer',
        'lead_id'                    => 'integer',
        'channel_id'                 => 'integer',
        'latest_activity_comment_id' => 'integer',
        'activity_comment_count'     => 'integer',
        'reminder_sent'              => 'boolean',
        'follow_up_datetime'         => 'datetime',
    ];

    protected $enum_casts = [
        'status'           => ActivityStatus::class,
        'follow_up_method' => ActivityFollowUpMethod::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::creating(function (self $model) {
            if (empty($model->customer_id)) $model->customer_id = $model->lead->customer_id;
            if (empty($model->channel_id)) $model->channel_id = $model->lead->channel_id;

            if (!empty($model->reminder_datetime)) $model->reminder_sent = false;
        });

        self::saving(function (self $model) {
            if ($model->getOriginal('reminder_datetime') != $model->reminder_datetime && !empty($model->reminder_datetime)) {
                $model->reminder_sent = false;
            }
        });

        parent::boot();
    }

    public static function createForOrder(Order $order): self
    {
        return self::create(
            [
                'order_id'           => $order->id,
                'lead_id'            => $order->lead_id,
                'customer_id'        => $order->customer_id,
                'channel_id'         => $order->channel_id,
                'user_id'            => $order->user_id,
                'follow_up_method'   => ActivityFollowUpMethod::OTHERS(),
                'status'             => ActivityStatus::HOT(),
                'follow_up_datetime' => now()
            ]
        );
    }

    /**
     * Override default tenanted trait.
     * Sales is scoped to its own activity.
     * Supervisor is scoped to the activity of its children sales.
     *
     * @param $query
     * @return mixed
     * @throws Exception
     */
    public function scopeTenanted($query)
    {
        $user = tenancy()->getUser();

        // we are still passing this to the trait as we want it to be scoped by the active tenant
        if ($user->is_sales) $query = $query->where('user_id', $user->id);
        if ($user->is_supervisor) $query = $query->whereIn('user_id', $user->getAllChildrenSales()->pluck('id'));

        return $this->defaultScopeTenanted($query);
    }

    /**
     * Determine whether currently authenticated user have access to this model
     * Default setting is user have access if the resource is allocated to a channel
     * that the user have access to
     * @param User|null $user
     * @return bool
     * @throws Exception
     */
    public function userCanAccess(User $user = null): bool
    {
        if (!$user) $user = tenancy()->getUser();
        if ($user->is_admin) return true;

        if ($user->is_sales) return $this->user_id == $user->id;
        if ($user->is_supervisor) in_array($this->user_id, $user->getAllChildrenSales()->pluck('id')->all());

        return false;
    }

    public function activityActivityComments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function latestComment()
    {
        return $this->belongsTo(ActivityComment::class, 'latest_activity_comment_id');
    }

    public function getFollowUpDatetimeAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setFollowUpDatetimeAttribute($value)
    {
        //if(!is_api()) $value = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
        $this->attributes['follow_up_datetime'] = $value ? Carbon::parse($value) : null;
    }

    public function scopeFollowUpDatetimeBefore($query, $datetime)
    {
        return $query->where('follow_up_datetime', '<=', Carbon::parse($datetime));
    }

    public function scopeFollowUpDatetimeAfter($query, $datetime)
    {
        return $query->where('follow_up_datetime', '>=', Carbon::parse($datetime));
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Refresh saved data for latest comment and comment count.
     */
    public function refreshCommentStats(): bool
    {
        $comments = $this->comments()->orderByDesc('id')->get('id');
        return $this->update(
            [
                'latest_activity_comment_id' => $comments->first()?->id,
                'activity_comment_count'     => $comments->count(),
            ]
        );
    }

    public function comments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
