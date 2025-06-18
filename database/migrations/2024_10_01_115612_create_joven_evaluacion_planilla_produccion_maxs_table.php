<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenEvaluacionPlanillaProduccionMaxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_evaluacion_planilla_produccion_maxs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('joven_evaluacion_planilla_id')->nullable();
            $table->foreign('joven_evaluacion_planilla_id')->references('id')->on('joven_evaluacion_planillas');
            $table->unsignedBigInteger('joven_evaluacion_planilla_produccion_id')->nullable();
            $table->foreign('joven_evaluacion_planilla_produccion_id')->references('id')->on('joven_evaluacion_planilla_produccions');
            $table->unsignedBigInteger('evaluacion_grupo_id')->nullable();
            $table->foreign('evaluacion_grupo_id')->references('id')->on('evaluacion_grupos');
            $table->integer('maximo')->nullable();
            $table->integer('minimo')->nullable();
            $table->integer('tope')->nullable();
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
        Schema::dropIfExists('joven_evaluacion_planilla_produccion_maxs');
    }
}
