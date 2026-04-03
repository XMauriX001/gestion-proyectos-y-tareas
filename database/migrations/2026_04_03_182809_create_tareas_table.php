<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id('id_tareas');
            $table->unsignedBigInteger('id_proyecto');
            $table->unsignedBigInteger('id_creado_por');
            $table->unsignedBigInteger('id_asignado_a')->nullable();
            $table->unsignedBigInteger('id_sprint')->nullable();
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_prioridad');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->timestamps();

            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('cascade');
            $table->foreign('id_creado_por')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_asignado_a')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_sprint')->references('id_sprint')->on('sprints')->onDelete('set null');
            $table->foreign('id_estado')->references('id_estado')->on('estado_tareas')->onDelete('restrict');
            $table->foreign('id_prioridad')->references('id_prioridad')->on('prioridad_tareas')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};