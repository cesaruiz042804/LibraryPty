<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear la tabla 'pedido' con los campos especificados
        // y las relaciones con las tablas 'libro' y 'users'.
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('libro_id')->nullable();
            $table->integer('stock')->default(0);
            $table->dateTime('fecha_pedido')->nullable();
            $table->datetime('fecha_entrega')->nullable();
            // Definir las claves foráneas para las relaciones
            $table->foreign('libro_id')->references('id')->on('libros')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
