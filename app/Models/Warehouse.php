<?php

namespace App\Models;

use App\Enum\Global\ActiveTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    public bool $inPermission = true;
    public array $basicOperations = ['create', 'read', 'update', 'delete'];

    protected $fillable = ['code', 'name', 'location', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => ActiveTypeEnum::class,
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }
}
