<?php

namespace App\Models;

use App\Enums\PersonTitle;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCustomer
 */
class Customer extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'customers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const TITLE_SELECT = [
        'Mr.'  => 'Mr.',
        'Mrs.' => 'Mrs.',
        'Ms.'  => 'Ms.',
    ];

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'description',
        'default_address_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'default_address_id' => 'integer',
        'date_of_birth'      => 'date',
    ];

    protected $enum_casts = [
        'title' => PersonTitle::class,
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function customerLeads()
    {
        return $this->hasMany(Lead::class, 'customer_id', 'id');
    }

    public function customerAddresses()
    {
        return $this->hasMany(Address::class, 'customer_id', 'id');
    }

    public function customerTaxInvoices()
    {
        return $this->hasMany(TaxInvoice::class, 'customer_id', 'id');
    }

    /**
     * @param $query
     * @param string $ids ids, comma separated
     * @return mixed
     */
    public function scopeIds($query, string $ids): mixed
    {
        return $query->whereIn('id', explode(',', $ids));
    }

    public function scopeWhereNameLike($query, $name)
    {
        return $query->where('first_name', 'LIKE', "%$name%")
                     ->orWhere('last_name', 'LIKE', "%$name%");
    }

    public function scopeWhereSearch($query, $key)
    {
        return $query->where('first_name', 'LIKE', "%$key%")
                     ->orWhere('last_name', 'LIKE', "%$key%")
                     ->orWhere('email', 'LIKE', "%$key%")
                     ->orWhere('phone', 'LIKE', "%$key%");
    }

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }
}
