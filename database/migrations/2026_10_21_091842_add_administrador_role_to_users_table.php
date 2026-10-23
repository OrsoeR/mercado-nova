<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite se utiliza en las pruebas automatizadas.
        // No necesita modificar el ENUM de MySQL.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'cliente',
                'empleado',
                'gerente',
                'administrador'
            ) NOT NULL DEFAULT 'cliente'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'cliente',
                'empleado',
                'gerente'
            ) NOT NULL DEFAULT 'cliente'
        ");
    }
};
