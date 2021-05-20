<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperProduct
 */
class Product extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsCompanyTenanted;

    public $table = 'products';

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
        'price',
        'is_active',
        'company_id',
        'product_brand_id',
        'product_model_id',
        'product_version_id',
        'product_category_code_id',
        'product_category_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price'                    => 'integer',
        'company_id'               => 'integer',
        'product_brand_id'         => 'integer',
        'product_model_id'         => 'integer',
        'product_version_id'       => 'integer',
        'product_category_code_id' => 'integer',
        'product_category_id'      => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['company_id', 'name'])
            ->saveSlugsTo('code');
    }

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::saved(function (self $model) {
            if (!empty($model->price) && $model->isDirty('price')) {
                $model->model->updatePriceRange($model->price);
            }
        });

        parent::boot();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function productProductUnits()
    {
        return $this->hasMany(ProductUnit::class, 'product_id', 'id');
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class, 'product_id', 'id');
    }

    public function productsActivities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag');
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }

    public function model()
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id');
    }

    public function version()
    {
        return $this->belongsTo(ProductVersion::class, 'product_version_id');
    }

    public function category_code()
    {
        return $this->belongsTo(ProductCategoryCode::class, 'product_category_code_id');
    }

    public function categoryCode()
    {
        return $this->category_code();
    }

    public function productListPivot()
    {
        return $this->hasMany(ProductProductList::class, 'product_id');
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

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeWhereTags($query, $csv_tags)
    {
        return $query->whereHas('tags', function ($query) use ($csv_tags) {
            $query->whereIn('slug', explode(',', $csv_tags));
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function toRecord()
    {

        $data           = $this->loadMissing(['brand', 'model', 'version', 'category_code'])->toArray();
        $data['images'] = $this->apiImages;

        unset(
            $data['created_at'], $data['updated_at'], $data['deleted_at'],
            $data['is_active'], $data['company_id'], $data['photo'],
            $data['media'],
        );

        // unset the nested product

        return $data;
    }
}
