<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Interfaces\Tenanted;
use App\Services\CoreService;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperChannel
 */
class Channel extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable;

    public $table = 'channels';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'channel_category_id',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {

            // Create stock of all product units for this channel
            CoreService::createStocksForChannel($model);
        });

        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        parent::boot();
    }

    public function scopeTenanted($query)
    {
        $activeTenant = activeTenant();

        if ($activeTenant) {
            return $query->where('id', activeTenant()->id ?? null);
        } else {
            return $query->whereIn('id', tenancy()->getTenants()->pluck('id'));
        }
    }

    public function scopeFindTenanted($query, int $id)
    {
        return $query->tenanted()->where('id', $id)->firstOrFail();
    }

    public function channelCatalogues()
    {
        return $this->hasMany(Catalogue::class, 'channel_id', 'id');
    }

    public function channelOrders()
    {
        return $this->hasMany(Order::class, 'channel_id', 'id');
    }

    public function channelStocks()
    {
        return $this->hasMany(Stock::class, 'channel_id', 'id');
    }

    public function channelLeads()
    {
        return $this->hasMany(Lead::class, 'channel_id', 'id');
    }

    public function channelsUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function channel_category()
    {
        return $this->belongsTo(ChannelCategory::class, 'channel_category_id');
    }

    public function channelCategory()
    {
        return $this->belongsTo(ChannelCategory::class, 'channel_category_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
