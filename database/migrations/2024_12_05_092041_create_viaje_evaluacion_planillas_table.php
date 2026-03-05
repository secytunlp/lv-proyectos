<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionPlanillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacion_planillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->nullable();
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->foreign('periodo_id')->references('id')->on('periodos');
            $table->enum('tipo', [
                'Formados',
                'En formación'
            ]);
            $table->enum('motivo', [
                'A',
                'B',
                'C'
            ]);

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
        Schema::dropIfExists('viaje_evaluacion_planillas');
    }
}
