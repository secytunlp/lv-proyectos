<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajePresupuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viaje_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viaje_id')->nullable();
            $table->foreign('viaje_id')->references('id')->on('viajes');
            $table->unsignedBigInteger('tipo_presupuesto_id')->nullable();
            $table->foreign('tipo_presupuesto_id')->references('id')->on('tipo_presupuestos');
            $table->string('detalle', 255)->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->datetime('fecha')->nullable();
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
        Schema::dropIfExists('viaje_presupuestos');
    }
}
