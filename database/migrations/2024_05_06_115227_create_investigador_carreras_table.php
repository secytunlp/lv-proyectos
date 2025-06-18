<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigadorCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigador_carreras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigador_id')->nullable();
            $table->foreign('investigador_id')->references('id')->on('investigadors');
            $table->unsignedBigInteger('carrerainv_id')->nullable();
            $table->foreign('carrerainv_id')->references('id')->on('carrerainvs');
            $table->unsignedBigInteger('organismo_id')->nullable();
            $table->foreign('organismo_id')->references('id')->on('organismos');
            $table->datetime('ingreso')->nullable();
            $table->boolean('actual')->default(true);
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
        Schema::dropIfExists('investigador_carreras');
    }
}
