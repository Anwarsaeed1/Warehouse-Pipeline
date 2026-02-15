<?php

namespace App\Models;

use App\Enum\Global\ActiveTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    public bool $inPermission = true;
    public array $basicOperations = ['create', 'read', 'update', 'delete'];

    protected $fillable = ['name', 'sku', 'description', 'price', 'low_stock_threshold', 'is_active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => ActiveTypeEnum::class,
        ];
    }

    /**
     * Get the stock associated with this inventory item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the stock transfers associated with this inventory item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }
}
