<?php

namespace App\Models;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends User
{

use HasFactory;

protected static function newFactory(): UsuarioFactory

{
    return UsuarioFactory::new();
}
    protected $table = 'users';

    /**
     * Convierte los nombres académicos a las columnas actuales.
     */
    public function getNombreAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    public function setNombreAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getApellidosAttribute(): ?string
    {
        return $this->attributes['apellidos'] ?? null;
    }

    public function setApellidosAttribute(?string $value): void
    {
        $this->attributes['apellidos'] = $value;
    }

    public function getCorreoAttribute(): ?string
    {
        return $this->attributes['email'] ?? null;
    }

    public function setCorreoAttribute(string $value): void
    {
        $this->attributes['email'] = $value;
    }

    public function getClaveAttribute(): ?string
    {
        return $this->attributes['password'] ?? null;
    }

    public function setClaveAttribute(string $value): void
    {
        $this->attributes['password'] = $value;
    }

    public function getRolAttribute(): ?string
    {
        return $this->attributes['role'] ?? null;
    }

    public function setRolAttribute(string $value): void
    {
        $this->attributes['role'] = $value;
    }
}
