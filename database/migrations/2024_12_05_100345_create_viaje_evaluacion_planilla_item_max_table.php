<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionPlanillaItemMaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacion_planilla_item_max', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_evaluacion_planilla_id')->nullable();
            $table->foreign('viaje_evaluacion_planilla_id')->references('id')->on('viaje_evaluacion_planillas');
            $table->unsignedBigInteger('viaje_evaluacion_planilla_item_id')->nullable();
            $table->foreign('viaje_evaluacion_planilla_item_id')->references('id')->on('viaje_evaluacion_planilla_items');
            $table->unsignedBigInteger('evaluacion_grupo_id')->nullable();
            $table->foreign('evaluacion_grupo_id')->references('id')->on('evaluacion_grupos');
            $table->integer('maximo')->nullable();
            $table->integer('minimo')->nullable();
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
        Schema::dropIfExists('viaje_evaluacion_planilla_item_max');
    }
}
