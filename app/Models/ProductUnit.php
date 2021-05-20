<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Services\CoreService;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductUnit
 */
class ProductUnit extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsCompanyTenanted;

    public $table = 'product_units';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'information',
        'detail',
        'price',
        'is_active',
        'sku',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price'      => 'integer',
        'product_id' => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            // Create stock of all channel
            CoreService::createStocksForProductUnit($model);
        });

        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        parent::boot();
    }

    public function productUnitItemProductUnits()
    {
        return $this->hasMany(ItemProductUnit::class, 'product_unit_id', 'id');
    }

    public function productUnitOrderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_unit_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function colour()
    {
        return $this->belongsTo(Colour::class);
    }

    public function covering()
    {
        return $this->belongsTo(Covering::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the properties for record purposes
     */
    public function toRecord(): array
    {
        $data = $this->toArray();

        unset(
            $data['created_at'], $data['updated_at'], $data['deleted_at'],
            $data['description'], $data['product']
        );

        return $data;
    }
}
