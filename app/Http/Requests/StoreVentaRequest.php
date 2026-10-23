<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'create',
            \App\Models\Venta::class
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],
            'fecha' => [
                'required',
                'date',
            ],
        ];
    }
}
