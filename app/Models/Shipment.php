<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperShipment
 */
class Shipment extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'shipments';

    protected $dates = [
        'estimated_delivery_date',
        'arrived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const STATUS_SELECT = [
        'waiting'    => 'Waiting',
        'dispatched' => 'Dispatched',
        'arrived'    => 'Arrived',
        'cancelled'  => 'Cancelled',
    ];

    protected $fillable = [
        'order_id',
        'fulfilled_by_id',
        'status',
        'note',
        'reference',
        'estimated_delivery_date',
        'arrived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function fulfilled_by()
    {
        return $this->belongsTo(User::class, 'fulfilled_by_id');
    }

    public function getEstimatedDeliveryDateAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setEstimatedDeliveryDateAttribute($value)
    {
        $this->attributes['estimated_delivery_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getArrivedAtAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setArrivedAtAttribute($value)
    {
        $this->attributes['arrived_at'] = $value ? Carbon::parse($value) : null;
    }
}
