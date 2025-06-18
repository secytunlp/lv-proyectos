<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenPlanillaPosgradoMaxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_planilla_posgrado_maxs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10)->nullable();
            $table->unsignedBigInteger('joven_planilla_id')->nullable();
            $table->foreign('joven_planilla_id')->references('id')->on('joven_planillas');
            $table->unsignedBigInteger('joven_planilla_posgrado_id')->nullable();
            $table->foreign('joven_planilla_posgrado_id')->references('id')->on('joven_planilla_posgrados');
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
        Schema::dropIfExists('joven_planilla_posgrado_maxs');
    }
}
