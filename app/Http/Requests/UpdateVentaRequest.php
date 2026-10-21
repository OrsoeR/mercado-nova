<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $venta = $this->route('venta');

        return $venta !== null
            && $this->user()?->can('update', $venta) === true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:productos,id',
            ],
            'fecha' => [
                'sometimes',
                'required',
                'date',
            ],
        ];
    }
}
