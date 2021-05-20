<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends BaseModel implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable;

    public $table = 'payments';

    protected $appends = [
        'proof',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const STATUS_SELECT = [
        'waiting'   => 'Waiting Approval',
        'rejeceted' => 'Rejected',
        'approved'  => 'Approved',
    ];

    protected $fillable = [
        'amount',
        'payment_type_id',
        'reference',
        'added_by_id',
        'approved_by_id',
        'status',
        'reason',
        'order_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function added_by()
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function getProofAttribute()
    {
        return $this->getMedia('proof');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
