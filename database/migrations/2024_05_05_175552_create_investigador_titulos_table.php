<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigadorTitulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigador_titulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigador_id')->nullable();
            $table->foreign('investigador_id')->references('id')->on('investigadors');
            $table->unsignedBigInteger('titulo_id')->nullable();
            $table->foreign('titulo_id')->references('id')->on('titulos');
            $table->datetime('egreso')->nullable();
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
        Schema::dropIfExists('investigador_titulos');
    }
}
