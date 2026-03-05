<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionPlanillaCategoriaMaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacion_planilla_categoria_max', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_evaluacion_planilla_id')->nullable();
            $table->foreign('viaje_evaluacion_planilla_id')->references('id')->on('viaje_evaluacion_planillas');
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->integer('maximo')->nullable();
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
        Schema::dropIfExists('viaje_evaluacion_planilla_categoria_max');
    }
}
