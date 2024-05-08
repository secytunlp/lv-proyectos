<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigadorSicadisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigador_sicadis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigador_id')->nullable();
            $table->foreign('investigador_id')->references('id')->on('investigadors');
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->integer('year')->nullable();
            $table->datetime('notificacion')->nullable();
            $table->boolean('actual')->default(true);
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
        Schema::dropIfExists('investigador_sicadis');
    }
}
