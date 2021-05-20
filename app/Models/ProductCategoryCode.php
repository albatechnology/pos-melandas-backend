<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperProductCategoryCode
 */
class ProductCategoryCode extends BaseModel
{
    use IsCompanyTenanted, SoftDeletes, Auditable;

    public $table = 'product_category_codes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    public function scopeWhereProductModelId($query, $ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->where('product_model_id', explode(',', $ids))->whereActive();
        });
    }

    public function scopeWhereProductBrandId($query, $ids)
    {
        return $query->whereHas('products', function ($query) use($ids){
            $query->whereIn('product_brand_id', explode(',', $ids))->whereActive();
        });
    }

    public function scopeWhereProductVersionId($query, $ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->where('product_version_id', explode(',', $ids))->whereActive();
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}