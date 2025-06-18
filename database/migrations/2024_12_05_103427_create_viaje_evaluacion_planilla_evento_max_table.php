<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionPlanillaEventoMaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacion_planilla_evento_max', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_evaluacion_planilla_id')->nullable();
            $table->foreign('viaje_evaluacion_planilla_id')->references('id')->on('viaje_evaluacion_planillas');
            $table->unsignedBigInteger('viaje_evaluacion_planilla_evento_id')->nullable();
            $table->foreign('viaje_evaluacion_planilla_evento_id')->references('id')->on('viaje_evaluacion_planilla_eventos');
            $table->unsignedBigInteger('evaluacion_grupo_id')->nullable();
            $table->foreign('evaluacion_grupo_id')->references('id')->on('evaluacion_grupos');
            $table->decimal('maximo', 10, 2)->nullable();
            $table->decimal('minimo', 10, 2)->nullable();
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
        Schema::dropIfExists('viaje_evaluacion_planilla_evento_max');
    }
}
