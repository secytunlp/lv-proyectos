<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegranteEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrante_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integrante_id')->constrained('integrantes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', [
                'Director', 'Codirector', 'Investigador Formado',
                'Investigador En Formación', 'Becario, Tesista', 'Colaborador'
            ]);
            $table->datetime('alta')->nullable();
            $table->datetime('baja')->nullable();
            $table->datetime('cambio')->nullable();
            $table->integer('horas')->nullable();

            $table->enum('estado', ['Alta Creada','Alta Recibida',' ','Baja Creada','Baja Recibida','Cambio Creado','Cambio Recibido','Cambio Hs. Creado','Cambio Hs. Recibido','Cambio Tipo Creado','Cambio Tipo Recibido'])->nullable();

            $table->text('consecuencias')->nullable();
            $table->text('motivos')->nullable();

            $table->text('reduccion')->nullable();

            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->unsignedBigInteger('sicadi_id')->nullable();
            $table->foreign('sicadi_id')->references('id')->on('sicadis');
            $table->enum('deddoc', ['Exclusiva','Semi Exclusiva','Simple']);
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->foreign('cargo_id')->references('id')->on('cargos');

            $table->unsignedBigInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultads');
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->foreign('unidad_id')->references('id')->on('unidads');
            $table->unsignedBigInteger('carrerainv_id')->nullable();
            $table->foreign('carrerainv_id')->references('id')->on('carrerainvs');
            $table->unsignedBigInteger('organismo_id')->nullable();
            $table->foreign('organismo_id')->references('id')->on('organismos');

            $table->enum('institucion', ['ANPCyT','CIC','CONICET','UNLP','OTRA','CIN']);
            $table->enum('beca', ['Beca inicial','Beca superior','Beca de entrenamiento','Beca doctoral','Beca posdoctoral','Beca finalización del doctorado','Beca maestría','Formación Superior','Iniciación','TIPO I','TIPO II','TIPO A','Tipo A - Maestría','Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)','Especial de Maestría','TIPO B','TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO','CONICET 2','RETENCION DE POSTGRADUADO','EVC']);
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
        Schema::dropIfExists('integrante_estados');
    }
}
