<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperStockTransfer
 */
class StockTransfer extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'stock_transfers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'stock_from_id',
        'stock_to_id',
        'requested_by_id',
        'approved_by_id',
        'amount',
        'item_from_id',
        'item_to_id',
        'item_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function stock_from()
    {
        return $this->belongsTo(Stock::class, 'stock_from_id');
    }

    public function stock_to()
    {
        return $this->belongsTo(Stock::class, 'stock_to_id');
    }

    public function requested_by()
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function item_from()
    {
        return $this->belongsTo(Item::class, 'item_from_id');
    }

    public function item_to()
    {
        return $this->belongsTo(Item::class, 'item_to_id');
    }
}
