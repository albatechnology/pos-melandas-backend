<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPaymentType
 */
class PaymentType extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'payment_types';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'code',
        'slug',
        'payment_category_id',
        'require_approval',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function paymentTypePayments()
    {
        return $this->hasMany(Payment::class, 'payment_type_id', 'id');
    }

    public function payment_category()
    {
        return $this->belongsTo(PaymentCategory::class, 'payment_category_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
