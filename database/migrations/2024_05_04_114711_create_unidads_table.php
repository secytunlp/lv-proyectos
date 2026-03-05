<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidads', function (Blueprint $table) {
            $table->id();
            $table->integer('tipo')->nullable();
            $table->unsignedBigInteger('padre_id')->nullable();
            $table->foreign('padre_id')->references('id')->on('unidads');
            $table->integer('hijos')->nullable();
            $table->string('nombre', 255)->nullable();
            $table->string('codigo', 15)->nullable();
            $table->string('sigla', 15)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->unsignedBigInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultads');
            $table->boolean('activa')->default(false);
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
        Schema::dropIfExists('unidads');
    }
}
