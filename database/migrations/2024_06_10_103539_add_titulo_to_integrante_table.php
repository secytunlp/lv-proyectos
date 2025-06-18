<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTituloToIntegranteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('integrantes', function (Blueprint $table) {
            $table->unsignedBigInteger('titulo_id')->nullable();
            $table->foreign('titulo_id')->references('id')->on('titulos');
            $table->unsignedBigInteger('titulopost_id')->nullable();
            $table->foreign('titulopost_id')->references('id')->on('titulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('integrantes', function (Blueprint $table) {
            //
        });
    }
}
