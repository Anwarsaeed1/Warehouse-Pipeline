<?php

namespace App\Http\Requests\Global\Other;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'page'     => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1',
        ];
    }
}
