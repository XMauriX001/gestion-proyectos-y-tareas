<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_projects', function (Blueprint $table) {
            $table->id('id_historial');
            $table->unsignedBigInteger('id_proyecto');
            $table->unsignedBigInteger('id_cambiado_por');
            $table->string('accion');
            $table->json('detalles')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('id_proyecto')->references('id_proyecto')->on('proyectos')->onDelete('cascade');
            $table->foreign('id_cambiado_por')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_projects');
    }
};