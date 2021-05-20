<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use App\Traits\ProductListable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductBrand
 */
class ProductBrand extends BaseModel implements HasMedia
{
    use IsCompanyTenanted, CustomInteractsWithMedia, ProductListable, Auditable, SoftDeletes;

    public $table = 'product_brands';

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
        'company_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

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

    public function colours()
    {
        return $this->hasMany(Colour::class, 'product_brand_id');
    }
}