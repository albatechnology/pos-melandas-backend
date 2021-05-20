<?php

namespace App\Models;

use App\Enums\OrderDetailStatus;
use App\Interfaces\DiscountableLine;
use App\Traits\Auditable;
use App\Traits\IsDiscountable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperOrderDetail
 */
class OrderDetail extends BaseModel implements HasMedia, DiscountableLine
{
    use SoftDeletes, InteractsWithMedia, Auditable, IsDiscountable;

    public $table = 'order_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'product_unit_id',
        'records',
        'order_id',
        'product_detail',
        'note',
        'quantity',
        'unit_price',
        'price',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'quantity'           => 'integer',
        'fulfilled_quantity' => 'integer',
        'unit_price'         => 'integer',
        'total_discount'     => 'integer',
        'total_price'        => 'integer',
        'order_id'           => 'integer',
        'company_id'         => 'integer',
        'records'            => 'json',
        'stock_fulfilment'   => 'json',
    ];

    protected $enum_casts = [
        'status' => OrderDetailStatus::class,
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function orderDetailsTargets()
    {
        return $this->belongsToMany(Target::class);
    }

    public function product_unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getProductUnitId(): int
    {
        return $this->product_unit_id;
    }
}
