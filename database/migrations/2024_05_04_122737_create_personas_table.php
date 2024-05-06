<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable();
            $table->string('apellido', 255)->nullable();

            $table->integer('documento')->nullable();
            $table->string('cuil', 13)->nullable();
            $table->enum('genero', ['F','MT','T','M','VT','NB','O','PN'])->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('calle', 50)->nullable();
            $table->string('nro', 10)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('depto', 10)->nullable();
            $table->string('localidad', 255)->nullable();
            $table->unsignedBigInteger('provincia_id')->nullable();
            $table->foreign('provincia_id')->references('id')->on('provincias');
            $table->string('cp', 10)->nullable();
            $table->text('foto')->nullable();
            $table->text('observaciones')->nullable();



            $table->datetime('nacimiento')->nullable();
            $table->datetime('fallecimiento')->nullable();

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
        Schema::dropIfExists('personas');
    }
}
