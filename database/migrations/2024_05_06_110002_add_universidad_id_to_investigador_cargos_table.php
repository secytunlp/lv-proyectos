<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniversidadIdToInvestigadorCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investigador_cargos', function (Blueprint $table) {
            $table->unsignedBigInteger('universidad_id')->nullable();
            $table->foreign('universidad_id')->references('id')->on('universidads');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investigador_cargos', function (Blueprint $table) {
            //
        });
    }
}
