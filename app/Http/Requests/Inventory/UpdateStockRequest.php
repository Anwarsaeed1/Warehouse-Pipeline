<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity'          => ['sometimes', 'integer', 'min:0'],
            'reserved_quantity' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reserved = $this->input('reserved_quantity');
            if ($reserved === null) {
                return;
            }
            $stock = $this->route('stock');
            $quantity = $this->input('quantity') ?? ($stock ? $stock->quantity : 0);
            if ((int) $reserved > (int) $quantity) {
                $validator->errors()->add('reserved_quantity', 'Reserved quantity cannot exceed quantity.');
            }
        });
    }
}
