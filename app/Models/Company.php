<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCompany
 */
class Company extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable, IsTenanted;

    public $table = 'companies';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::created(function (self $model) {
            $model->companyData()->create([]);
        });

        parent::boot();
    }

    /**
     * Override tenanted scope.
     *
     * @param $query
     * @return mixed
     */
    public function scopeTenanted($query): mixed
    {
        $hasActiveChannel = tenancy()->getActiveTenant();
        $hasActiveCompany = tenancy()->getActiveCompany();
        $isAdmin          = tenancy()->getUser()->is_admin;

        if ($hasActiveChannel) return $query->where('id', $hasActiveChannel->company->id);
        if (!$isAdmin) return $query->where('id', tenancy()->getUser()->company_id);
        if ($hasActiveCompany) return $query->where('id', $hasActiveCompany->id);

        return $query;
    }

    public function tenant()
    {
        return null;
    }

    public function companyChannels()
    {
        return $this->hasMany(Channel::class, 'company_id', 'id');
    }

    public function companyProducts()
    {
        return $this->hasMany(Product::class, 'company_id', 'id');
    }

    public function companyItems()
    {
        return $this->hasMany(Item::class, 'company_id', 'id');
    }

    public function companyProductCategories()
    {
        return $this->hasMany(ProductCategory::class, 'company_id', 'id');
    }

    public function companyProductTags()
    {
        return $this->hasMany(ProductTag::class, 'company_id', 'id');
    }

    public function companyDiscounts()
    {
        return $this->hasMany(Discount::class, 'company_id', 'id');
    }

    public function companyPromos()
    {
        return $this->hasMany(Promo::class, 'company_id', 'id');
    }

    public function companyBanners()
    {
        return $this->hasMany(Banner::class, 'company_id', 'id');
    }

    public function companyPaymentCategories()
    {
        return $this->hasMany(PaymentCategory::class, 'company_id', 'id');
    }

    public function companyPaymentTypes()
    {
        return $this->hasMany(PaymentType::class, 'company_id', 'id');
    }

    public function companyData()
    {
        return $this->hasOne(CompanyData::class, 'company_id', 'id');
    }

    public function companiesUsers()
    {
        return $this->belongsToMany(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Determine whether currently authenticated user have access to this model
     * Default setting is user have access if the resource is allocated to a channel
     * that the user have access to
     * @param User|null $user
     * @return bool
     * @throws Exception
     */
    public function userCanAccess(User $user = null): bool
    {
        if(!$user) $user = tenancy()->getUser();
        if ($user->is_admin) return true;

        return $user->company_id == $this->id;
    }
}
