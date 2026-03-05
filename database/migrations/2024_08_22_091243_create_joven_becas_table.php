<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovenBecasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joven_becas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('joven_id')->nullable();
            $table->foreign('joven_id')->references('id')->on('jovens');
            $table->enum('institucion', ['ANPCyT','CIC','CONICET','UNLP','OTRA','CIN']);
            $table->enum('beca', ['Beca inicial','Beca superior','Beca de entrenamiento','Beca doctoral','Beca posdoctoral','Beca finalización del doctorado','Beca maestría','Formación Superior','Iniciación','TIPO I','TIPO II','TIPO A','Tipo A - Maestría','Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)','Especial de Maestría','TIPO B','TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO','CONICET 2','RETENCION DE POSTGRADUADO','EVC']);
            $table->datetime('desde')->nullable();
            $table->datetime('hasta')->nullable();
            $table->boolean('unlp')->default(false);
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->foreign('unidad_id')->references('id')->on('unidads');
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
        Schema::dropIfExists('joven_becas');
    }
}
