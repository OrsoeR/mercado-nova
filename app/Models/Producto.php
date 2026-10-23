<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'existencia',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'existencia' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(
            Categoria::class,
            'categoria_producto',
            'producto_id',
            'categoria_id'
        )->withTimestamps();
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'producto_id');
    }
}
