<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenEvaluacionPuntajeAnteriorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_evaluacion_puntaje_anteriors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('joven_evaluacion_id')->nullable();
            $table->foreign('joven_evaluacion_id')->references('id')->on('joven_evaluacions');
            $table->unsignedBigInteger('joven_evaluacion_planilla_id')->nullable();
            $table->foreign('joven_evaluacion_planilla_id')->references('id')->on('joven_evaluacion_planillas');
            $table->unsignedBigInteger('joven_evaluacion_planilla_anterior_max_id')->nullable();
            $table->foreign('joven_evaluacion_planilla_anterior_max_id')->references('id')->on('joven_evaluacion_planilla_anterior_maxs');
            $table->decimal('puntaje', 10, 2)->nullable();
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
        Schema::dropIfExists('joven_evaluacion_puntaje_anteriors');
    }
}
