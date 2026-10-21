<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Roles disponibles
    public const ROLE_CLIENTE = 'cliente';
    public const ROLE_EMPLEADO = 'empleado';
    public const ROLE_GERENTE = 'gerente';
    public const ROLE_ADMINISTRADOR = 'administrador';

    public const ROLES = [
        self::ROLE_CLIENTE => 'Cliente',
        self::ROLE_EMPLEADO => 'Empleado',
        self::ROLE_GERENTE => 'Gerente',
        self::ROLE_ADMINISTRADOR => 'Administrador',
    ];

    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Comprueba si el usuario tiene alguno de los roles indicados.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isCliente(): bool
    {
        return $this->role === self::ROLE_CLIENTE;
    }

    public function isEmpleado(): bool
    {
        return $this->role === self::ROLE_EMPLEADO;
    }

    public function isGerente(): bool
    {
        return $this->role === self::ROLE_GERENTE;
    }

    public function isAdministrador(): bool
    {
        return $this->role === self::ROLE_ADMINISTRADOR;
    }

    /**
     * Devuelve el nombre legible del rol.
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst((string) $this->role);
    }

    /**
     * Devuelve la ruta correspondiente al dashboard.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMINISTRADOR => 'admin.dashboard',
            self::ROLE_GERENTE => 'gerente.dashboard',
            self::ROLE_EMPLEADO => 'empleado.dashboard',
            default => 'cliente.dashboard',
        };
    }

    /**
     * Productos publicados por este usuario.
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'usuario_id');
    }

    /**
     * Ventas en las que este usuario es el cliente.
     */
    public function ventasComoCliente(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    /**
     * Ventas en las que este usuario es el vendedor.
     */
    public function ventasComoVendedor(): HasMany
    {
        return $this->hasMany(Venta::class, 'vendedor_id');
    }

    /**
     * Categorías asociadas a los productos de este usuario.
     */
    public function categoriasDeProductos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Categoria::class,
            Producto::class,
            'usuario_id',
            'id',
            'id',
            'id'
        )
        ->join(
            'categoria_producto',
            'categorias.id',
            '=',
            'categoria_producto.categoria_id'
        )
        ->whereColumn(
            'categoria_producto.producto_id',
            'productos.id'
        )
        ->select('categorias.*')
        ->distinct();
    }
}
