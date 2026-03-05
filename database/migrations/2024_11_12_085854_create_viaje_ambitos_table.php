<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeAmbitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_ambitos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_id')->nullable();
            $table->foreign('viaje_id')->references('id')->on('viajes');
            $table->datetime('desde')->nullable();
            $table->datetime('hasta')->nullable();
            $table->string('institucion', 255)->nullable();
            $table->string('ciudad', 255)->nullable();
            $table->string('pais', 255)->nullable();
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
        Schema::dropIfExists('viaje_ambitos');
    }
}
