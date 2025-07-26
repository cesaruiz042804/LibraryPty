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
        // Crear la tabla 'libro' con los campos especificados
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('autor');
            $table->integer('anio_publicacion')->nullable();
            $table->string('isbn')->nullable(); // Códuigo de barra
            $table->string('sinopsis')->nullable();
            $table->integer('stock')->default(1);
            $table->decimal('precio_base', 8, 2)->nullable(); // Puedes ajustar la precisión (8,2) y si es nullable
            $table->decimal('precio_final', 8, 2)->nullable();// Se añade después de precio_base
            $table->enum('status', ['activo', 'inactivo'])->default('activo'); // Estados del libro
            $table->string('imagen')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); // Add onDelete for good practice
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
