<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use App\Traits\ProductListable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductModel
 */
class ProductModel extends BaseModel implements HasMedia
{
    use IsCompanyTenanted, CustomInteractsWithMedia, ProductListable, SoftDeletes, Auditable;

    public $table = 'product_models';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'price_min'  => 'integer',
        'price_max'  => 'integer',
    ];

    public function updatePriceRange(int $price = null)
    {
        if (is_null($price)) {
            $prices = $this->products->pluck('price');

            $this->update(
                [
                    'price_min' => $prices->min(),
                    'price_max' => $prices->max(),
                ]
            );
        } else {
            if ($this->price_min == 0 || $this->price_min > $price) $this->price_min = $price;
            if ($this->price_max < $price) $this->price_max = $price;

            $this->save();
        }
    }

    public function scopeWhereProductBrandId($query, $ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->whereIn('product_brand_id', explode(',', $ids))->whereActive();
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
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
}