<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->id('id_sprint');
            $table->unsignedBigInteger('id_proyecto');
            $table->unsignedBigInteger('id_creado_por');
            $table->unsignedBigInteger('id_estado');
            $table->string('titulo');
            $table->date('fecha_inicio');
            $table->date('fecha_final');
            $table->timestamps();

            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('cascade');
            $table->foreign('id_creado_por')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_estado')->references('id_estado')->on('estado_sprints')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};