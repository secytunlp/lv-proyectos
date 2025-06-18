<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacultadIdAndCuilToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar el campo facultad_id
            $table->unsignedInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultad')->onDelete('set null');

            // Agregar el campo cuil
            $table->string('cuil', 13)->unique()->nullable()->comment('Formato: XX-XXXXXXXX-X');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
