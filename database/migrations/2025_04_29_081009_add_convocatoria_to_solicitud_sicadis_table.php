<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConvocatoriaToSolicitudSicadisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitud_sicadis', function (Blueprint $table) {
            $table->unsignedBigInteger('convocatoria_id')->nullable();
            $table->foreign('convocatoria_id')->references('id')->on('sicadi_convocatorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitud_sicadis', function (Blueprint $table) {
            //
        });
    }
}
