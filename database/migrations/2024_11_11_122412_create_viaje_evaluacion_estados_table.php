<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacion_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_evaluacion_id')->constrained('viaje_evaluacions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_name', 255)->nullable();
            $table->enum('estado', ['Creada','Recibida','Aceptada','Rechazada','En Evaluación','Evaluada','Rectificada'])->nullable();
            $table->string('comentarios', 255)->nullable();
            $table->datetime('desde')->nullable();
            $table->datetime('hasta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viaje_evaluacion_estados');
    }
}
