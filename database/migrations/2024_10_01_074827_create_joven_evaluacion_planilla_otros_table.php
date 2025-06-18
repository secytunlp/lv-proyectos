<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenEvaluacionPlanillaOtrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_evaluacion_planilla_otros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable();
            $table->unsignedBigInteger('evaluacion_subgrupo_id')->nullable();
            $table->foreign('evaluacion_subgrupo_id')->references('id')->on('evaluacion_subgrupos');
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
        Schema::dropIfExists('joven_evaluacion_planilla_otros');
    }
}
