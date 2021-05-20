<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAddress
 */
class Address extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'addresses';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = ['id'];

    protected $enum_casts = [
        'type' => AddressType::class,
    ];

    public function addressOrders()
    {
        return $this->hasMany(Order::class, 'address_id', 'id');
    }

    public function addressTaxInvoices()
    {
        return $this->hasMany(TaxInvoice::class, 'address_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the properties for record purposes
     */
    public function toRecord()
    {
        $data = $this->toArray();
        unset($data['created_at'], $data['updated_at'], $data['deleted_at']);

        return $data;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
