<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTarget
 */
class Target extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'targets';

    const SUBJECT_TYPE_SELECT = [
        'sum'  => 'Sum',
        'each' => 'Each',
    ];

    const VALUE_TYPE_SELECT = [
        'price' => 'Order price',
        'count' => 'Count',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const SCOPE_MODEL_SELECT = [
        'company' => 'Company',
        'channel' => 'Channel',
        'global'  => 'Global',
    ];

    const SUBJECT_SELECT = [
        'company'    => 'Company',
        'channel'    => 'Channel',
        'supervisor' => 'Supervisor',
        'sales'      => 'Sales',
    ];

    const TYPE_SELECT = [
        'income'   => 'Income',
        'promo'    => 'Promo',
        'activity' => 'Activity',
        'deal'     => 'Deal',
        'discount' => 'Discount',
        'category' => 'Category',
        'product'  => 'Product',
    ];

    protected $fillable = [
        'start_date',
        'end_date',
        'name',
        'value',
        'type',
        'type_identifier',
        'subject',
        'subject_type',
        'scope_model',
        'scope_identifier',
        'value_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getStartDateAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        if(!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function order_details()
    {
        return $this->belongsToMany(OrderDetail::class);
    }
}
