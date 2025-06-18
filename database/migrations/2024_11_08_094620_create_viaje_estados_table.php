<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajeEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['Creada','Recibida','Admitida','No Admitida','Otorgada-No-Rendida','En evaluación','No otorgada','Evaluada','Otorgada-Rendida','Otorgada-Renunciada','Retirada','Otorgada-Devuelta'])->nullable();
            $table->string('comentarios', 255)->nullable();
            $table->string('user_name', 255)->nullable();
            $table->datetime('desde')->nullable();
            $table->datetime('hasta')->nullable();
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
        Schema::dropIfExists('viaje_estados');
    }
}
