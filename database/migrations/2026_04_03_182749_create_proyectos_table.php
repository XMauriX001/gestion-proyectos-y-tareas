<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->uuid('id_proyecto')->primary();
            $table->uuid('creado_por');
            $table->unsignedBigInteger('id_estado');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_final');
            $table->timestamps();

            $table->foreign('creado_por')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_estado')->references('id_estado')->on('estado_proyectos')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
