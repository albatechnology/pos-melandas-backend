<?php

namespace App\Models;

use App\Exceptions\InsufficientStockException;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsTenanted;
use DateTimeInterface;

/**
 * @mixin IdeHelperStock
 */
class Stock extends BaseModel implements Tenanted
{
    use Auditable, IsTenanted;

    public $table = 'stocks';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'channel_id',
        'stock',
        'product_unit_id',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function stockFromStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'stock_from_id', 'id');
    }

    public function stockToStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'stock_to_id', 'id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function addStock(int $value)
    {
        if ($this->stock + $value < 0) {
            throw new InsufficientStockException();
        }

        $this->increment('stock', $value);
    }
}
