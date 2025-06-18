<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['I+D','PPID','PIIT-AP','PIO'])->nullable();
            $table->enum('estado', ['Creado','Recibido','Admitido','No Admitido','Acreditado','En evaluación','No acreditado','Evaluado','Retirado'])->nullable();
            $table->string('codigo', 20)->nullable();
            $table->string('sigeva', 20)->nullable();
            $table->string('titulo', 255)->nullable();
            $table->datetime('inicio')->nullable();
            $table->datetime('fin')->nullable();
            $table->unsignedBigInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultads');
            $table->integer('duracion')->nullable();
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->foreign('unidad_id')->references('id')->on('unidads');
            $table->unsignedBigInteger('campo_id')->nullable();
            $table->foreign('campo_id')->references('id')->on('campos');
            $table->unsignedBigInteger('disciplina_id')->nullable();
            $table->foreign('disciplina_id')->references('id')->on('disciplinas');
            $table->enum('investigacion', ['Aplicada','Básica','Desarrollo','Creación'])->nullable();
            $table->string('linea', 255)->nullable();
            $table->text('resumen')->nullable();
            $table->string('clave1', 255)->nullable();
            $table->string('clave2', 255)->nullable();
            $table->string('clave3', 255)->nullable();
            $table->string('clave4', 255)->nullable();
            $table->string('clave5', 255)->nullable();
            $table->string('clave6', 255)->nullable();
            $table->string('key1', 255)->nullable();
            $table->string('key2', 255)->nullable();
            $table->string('key3', 255)->nullable();
            $table->string('key4', 255)->nullable();
            $table->string('key5', 255)->nullable();
            $table->string('key6', 255)->nullable();
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
        Schema::dropIfExists('proyectos');
    }
}
