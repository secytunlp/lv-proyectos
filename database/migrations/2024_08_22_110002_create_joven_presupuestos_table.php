<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenPresupuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('joven_id')->nullable();
            $table->foreign('joven_id')->references('id')->on('jovens');
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
        Schema::dropIfExists('joven_presupuestos');
    }
}
