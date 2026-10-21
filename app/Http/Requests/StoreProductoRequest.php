<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'create',
            Producto::class
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
            'descripcion' => [
                'required',
                'string',
                'max:2000',
            ],
            'precio' => [
                'required',
                'numeric',
                'min:0.01',
                'max:99999999.99',
            ],
            'existencia' => [
                'required',
                'integer',
                'min:0',
                'max:2147483647',
            ],
            'categorias' => [
                'sometimes',
                'array',
            ],
            'categorias.*' => [
                'integer',
                'exists:categorias,id',
            ],
        ];
    }
}
