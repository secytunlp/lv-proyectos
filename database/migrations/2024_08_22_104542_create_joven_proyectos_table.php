<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_proyectos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('joven_id')->nullable();
            $table->foreign('joven_id')->references('id')->on('jovens');
            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->datetime('desde')->nullable();
            $table->datetime('hasta')->nullable();
            $table->string('codigo', 20)->nullable();
            $table->string('director', 100)->nullable();
            $table->string('titulo', 255)->nullable();
            $table->boolean('agregado')->default(false);
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
        Schema::dropIfExists('joven_proyectos');
    }
}
