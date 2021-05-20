<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTaxInvoice
 */
class TaxInvoice extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'tax_invoices';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'customer_id',
        'company_name',
        'npwp',
        'address_id',
        'email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function taxInvoiceOrders()
    {
        return $this->hasMany(Order::class, 'tax_invoice_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
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
}
