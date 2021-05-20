<?php

namespace App\Models;

use App\Enums\DiscountError;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Interfaces\Discountable;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use App\Traits\IsDiscountable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperOrder
 */
class Order extends BaseModel implements Tenanted, Discountable
{
    use SoftDeletes, Auditable, IsCompanyTenanted, IsDiscountable;

    public $table = 'orders';

    protected $fillable = [
        'raw_source',
    ];

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'total_discount'   => 'integer',
        'total_price'      => 'integer',
        'user_id'          => 'integer',
        'lead_id'          => 'integer',
        'customer_id'      => 'integer',
        'channel_id'       => 'integer',
        'activity_id'      => 'integer',
        'tax_invoice_sent' => 'integer',
        'shipping_fee'     => 'integer',
        'packing_fee'      => 'integer',
        'records'          => 'json',
        'raw_source'       => 'json',
    ];

    protected $enum_casts = [
        'discount_error' => DiscountError::class,
        'payment_status' => OrderPaymentStatus::class,
        'status'         => OrderStatus::class,
    ];

    public ?int $expected_price = null;

    public function orderOrderTrackings()
    {
        return $this->hasMany(OrderTracking::class, 'order_id', 'id');
    }

    public function orderOrderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function orderShipments()
    {
        return $this->hasMany(Shipment::class, 'order_id', 'id');
    }

    public function orderPayments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    public function ordersTargets()
    {
        return $this->belongsToMany(Target::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function billing_address()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shipping_address()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function activity()
    {
        return $this->hasOne(Activity::class);
    }

    public function getDiscountableLines(): Collection
    {
        return $this->order_details;
    }

    public function getOriginalPriceAttribute()
    {
        return ($this->total_price ?? 0) +
            ($this->total_discount ?? 0) +
            ($this->shipping_fee ?? 0) +
            ($this->packing_fee ?? 0);
    }

    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
