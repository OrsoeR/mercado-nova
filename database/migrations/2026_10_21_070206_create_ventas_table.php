
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('cliente_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('fecha');
            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
