<?php

namespace App\Http\Requests\StockTransfer;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'integer', Rule::exists('warehouses', 'id')],
            'to_warehouse_id'   => ['required', 'integer', Rule::exists('warehouses', 'id'), 'different:from_warehouse_id'],
            'inventory_item_id' => ['required', 'integer', Rule::exists('inventory_items', 'id')],
            'quantity'          => ['required', 'integer', 'min:1'],
            'note'              => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->from_warehouse_id && $this->inventory_item_id && $this->quantity) {
                $stock = Stock::query()
                    ->where('warehouse_id', $this->from_warehouse_id)
                    ->where('inventory_item_id', $this->inventory_item_id)
                    ->first();

                $available = $stock ? max(0, $stock->quantity - $stock->reserved_quantity) : 0;

                if ((int) $this->quantity > (int) $available) {
                    $validator->errors()->add('quantity', __('api.quantity_exceeds_available', ['available' => $available]));
                }
            }
        });
    }
}
