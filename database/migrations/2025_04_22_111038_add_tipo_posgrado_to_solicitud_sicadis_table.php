<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPosgradoToSolicitudSicadisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitud_sicadis', function (Blueprint $table) {
            $table->enum('tipo_posgrado', [
                'Doctor/a',
                'Magíster'
            ]);
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
