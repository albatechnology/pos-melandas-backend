<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Interfaces\Tenanted;
use App\Jobs\LeadStatusChange;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperLead
 */
class Lead extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable;

    use IsTenanted {
        IsTenanted::scopeTenanted as protected defaultScopeTenanted;
    }

    public $table = 'leads';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'status',
        'is_new_customer',
        'label',
        'user_id',
        'group_id',
        'customer_id',
        'channel_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_new_customer' => 'bool',
        'group_id'        => 'integer',
        'user_id'         => 'integer',
        'customer_id'     => 'integer',
        'channel_id'      => 'integer',
        'status_history'  => 'array',
    ];

    protected $enum_casts = [
        'type'   => LeadType::class,
        'status' => LeadStatus::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            if (empty($model->group_id)) {
                $model->update(["group_id" => $model->determineGroupId()]);
            }
        });

        self::saving(function (self $model) {

            // auto generate label when not given
            if (empty($model->label)) {
                $time         = $model->created_at ?? now();
                $name         = $model->customer->full_name;
                $model->label = sprintf("%s - %s", $time->format('Y-m-d'), $name);
            }

            // when a lead is closed as a sale, close other lead in the group
            if (
                $model->getOriginal('type') !== $model->type
                && $model->type->is(LeadType::CLOSED)
                && $model->status->is(LeadStatus::SALES)
            ) {
                // do a mass update to avoid recursive update
                Lead::where('group_id', $model->group_id)->update(
                    [
                        'type'   => LeadType::CLOSED,
                        'status' => LeadStatus::OTHER_SALES
                    ]
                );
            }

            if ($model->isDirty('status') || $model->isDirty('type')) {
                $model->addStatusHistory();
            }
        });


        parent::boot();
    }

    /**
     * Determine the appropriate group id for the model.
     * This is done by taking the previous lead for this lead customer and:
     * 1. Use current id as group id if previous lead group has closed sales lead, otherwise:
     * 2. Use the previous lead group id as this lead's group id
     */
    public function determineGroupId(): int
    {
        // since all leads in the same group is closed together,
        // it is fine to just grab first lead in the latest lead group
        $latestCustomerLeadGroup = Lead::where('customer_id', $this->customer_id)
                                       ->orderBy('group_id', 'desc')
                                       ->get();

        $hasClosedSale = $latestCustomerLeadGroup->contains(function (Lead $lead) {
            return $lead->status->is(LeadStatus::SALES);
        });

        return $hasClosedSale ? $this->id : $latestCustomerLeadGroup->first()->group_id;
    }

    /**
     * Override default tenanted trait.
     * Sales is scoped to its own leads.
     * Supervisor is scope to the leads of its children sales.
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function setNameAttribute($value)
    {
        return $this->label = $value;
    }

    public function getNameAttribute()
    {
        return $this->label;
    }

    public function scopeCustomerName($query, $name)
    {
        return $query->whereHas('customer', function ($query) use ($name) {
            return $query->whereNameLike($name);
        });
    }

    public function scopeChannelName($query, $name)
    {
        return $query->whereHas('channel', function ($query) use ($name) {
            return $query->where('name', 'LIKE', "%$name%");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLatestActivityAttribute()
    {
        return $this->leadActivities()->orderBy('id', 'desc')->first();
    }

    public function leadActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'lead_id', 'id');
    }

    public function scopeCustomerSearch($query, $key)
    {
        $query->whereHas('customer', fn($q) => $q->whereSearch($key));
    }

    /**
     * Update to next status now and queue next status at the same time (if applicable)
     * @param LeadStatus|null $from_status
     * @return null
     * @throws Exception
     */
    public function nextStatusAndQueue(LeadStatus $from_status = null)
    {
        // safety check, status may have change while on queue
        if (!is_null($from_status) && $this->status->isNot($from_status)) return null;

        // if we have reached the end of status progression
        if (!$next_status = $this->status->nextStatus()) return null;

        // check the status due at (this may be removed as a method to cancel status change)
        // or extended when a new queue is placed
        if (is_null($this->status_change_due_at) || $this->status_change_due_at > now()) return null;

        // status change
        $this->update([
            'status'                    => $next_status,
            'status_change_due_at'      => null,
            'has_pending_status_change' => 0
        ]);

        // if there is still further status progression, we continue queuing for next status
        $this->queueStatusChange();

        return null;
    }

    /**
     * Queue this lead for next status change (counting time starting now)
     * @throws Exception
     */
    public function queueStatusChange()
    {
        // if we have reached the end of status progression
        if (!$next_status = $this->status->nextStatus()) return null;

        $dispatch_at = now()->addSeconds($this->status->delayDuration());

        $this->update(
            [
                'status_change_due_at'      => $dispatch_at,
                'has_pending_status_change' => 0
            ]
        );

        LeadStatusChange::dispatch($this, $this->status)->delay($dispatch_at);
    }

    public function addStatusHistory(): array
    {
        return $this->status_history = [...$this->status_history ?? [], [
            'status'     => $this->status->value,
            'type'       => $this->type->value,
            'updated_at' => now()
        ]];
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
