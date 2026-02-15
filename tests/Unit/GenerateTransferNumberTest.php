<?php

namespace Tests\Unit;

use App\Enum\Inventory\StockTransferStatusEnum;
use App\Models\StockTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateTransferNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_transfer_number_format(): void
    {
        $number = generateTransferNumber();

        $this->assertStringStartsWith('TRF-', $number);
        $this->assertMatchesRegularExpression('/^TRF-\d{8}-\d{4}$/', $number);
    }

    public function test_generate_transfer_number_increments_sequence_same_day(): void
    {
        $warehouseFrom = \App\Models\Warehouse::factory()->create();
        $warehouseTo = \App\Models\Warehouse::factory()->create();
        $item = \App\Models\InventoryItem::factory()->create();
        $prefix = 'TRF-' . now()->format('Ymd') . '-';
        StockTransfer::create([
            'transfer_number'    => $prefix . '0001',
            'from_warehouse_id'  => $warehouseFrom->id,
            'to_warehouse_id'    => $warehouseTo->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 1,
            'status'            => StockTransferStatusEnum::Completed,
        ]);

        $number = generateTransferNumber();

        $this->assertStringStartsWith($prefix, $number);
        $seq = (int) substr($number, strlen($prefix));
        $this->assertGreaterThanOrEqual(2, $seq);
    }
}
