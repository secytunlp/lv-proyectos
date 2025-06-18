<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEvaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_evaluacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_id')->nullable();
            $table->foreign('viaje_id')->references('id')->on('viajes');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('user_name', 255)->nullable();
            $table->string('user_cuil', 13)->nullable();
            $table->enum('estado', ['Creada','Recibida','Aceptada','Rechazada','En Evaluación','Evaluada','Rectificada'])->nullable();
            $table->datetime('fecha')->nullable();
            $table->boolean('interno')->default(false);
            $table->decimal('puntaje', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('viaje_evaluacions');
    }
}
