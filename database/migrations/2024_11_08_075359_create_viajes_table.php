<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigador_id')->nullable();
            $table->foreign('investigador_id')->references('id')->on('investigadors');
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->foreign('periodo_id')->references('id')->on('periodos');
            $table->unsignedBigInteger('proyecto1_id')->nullable();
            $table->foreign('proyecto1_id')->references('id')->on('proyectos');
            $table->unsignedBigInteger('proyecto2_id')->nullable();
            $table->foreign('proyecto2_id')->references('id')->on('proyectos');
            $table->enum('estado', ['Creada','Recibida','Admitida','No Admitida','Otorgada-No-Rendida','En evaluación','No otorgada','Evaluada','Otorgada-Rendida','Otorgada-Renunciada','Retirada','Otorgada-Devuelta'])->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('notificacion')->default(true);
            $table->string('telefono', 30)->nullable();
            $table->datetime('fecha')->nullable();
            $table->string('calle', 50)->nullable();
            $table->string('nro', 10)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('depto', 10)->nullable();
            $table->string('cp', 10)->nullable();
            $table->unsignedBigInteger('titulo_id')->nullable();
            $table->foreign('titulo_id')->references('id')->on('titulos');
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->foreign('unidad_id')->references('id')->on('unidads');
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->foreign('cargo_id')->references('id')->on('cargos');
            $table->enum('deddoc', ['Exclusiva','Semi Exclusiva','Simple']);
            $table->unsignedBigInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultads');
            $table->unsignedBigInteger('facultadplanilla_id')->nullable();
            $table->foreign('facultadplanilla_id')->references('id')->on('facultads');
            $table->unsignedBigInteger('carrerainv_id')->nullable();
            $table->foreign('carrerainv_id')->references('id')->on('carrerainvs');
            $table->unsignedBigInteger('organismo_id')->nullable();
            $table->foreign('organismo_id')->references('id')->on('organismos');
            $table->datetime('ingreso_carrerainv')->nullable();
            $table->unsignedBigInteger('unidadcarrera_id')->nullable();
            $table->foreign('unidadcarrera_id')->references('id')->on('unidads');
            $table->enum('institucion', ['ANPCyT','CIC','CONICET','UNLP','OTRA','CIN']);
            $table->enum('beca', ['Beca inicial','Beca superior','Beca de entrenamiento','Beca doctoral','Beca posdoctoral','Beca finalización del doctorado','Beca maestría','Formación Superior','Iniciación','TIPO I','TIPO II','TIPO A','Tipo A - Maestría','Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)','Especial de Maestría','TIPO B','TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO','CONICET 2','RETENCION DE POSTGRADUADO','EVC']);
            $table->string('periodobeca', 255)->nullable();
            $table->boolean('unlp')->default(false);
            $table->unsignedBigInteger('unidadbeca_id')->nullable();
            $table->foreign('unidadbeca_id')->references('id')->on('unidads');
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->unsignedBigInteger('sicadi_id')->nullable();
            $table->foreign('sicadi_id')->references('id')->on('sicadis');
            $table->enum('tipo', [
                'Investigador Formado',
                'Investigador En Formación'
            ]);
            $table->enum('motivo', [
                'A) Reuniones Científicas:',
                'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP:',
                'C) ESTADÍA DE TRABAJO EN LA UNLP PARA UN INVESTIGADOR INVITADO:'
            ]);
            $table->text('objetivo')->nullable();
            $table->string('curriculum', 255)->nullable();
            $table->string('trabajo', 255)->nullable();
            $table->string('aceptacion', 255)->nullable();
            $table->string('titulotrabajo', 255)->nullable();
            $table->string('autores', 255)->nullable();
            $table->string('congresonombre', 255)->nullable();
            $table->string('lugartrabajo', 255)->nullable();
            $table->datetime('trabajodesde')->nullable();
            $table->datetime('trabajohasta')->nullable();
            $table->text('resumen')->nullable();
            $table->text('relevancia')->nullable();
            $table->string('invitacion', 255)->nullable();
            $table->text('modalidad')->nullable();
            $table->string('aval', 255)->nullable();
            $table->string('actividades', 255)->nullable();
            $table->string('convenio', 255)->nullable();
            $table->string('cvprofesor', 255)->nullable();
            $table->string('profesor', 255)->nullable();
            $table->string('lugarprofesor', 255)->nullable();
            $table->text('libros')->nullable();
            $table->text('compilados')->nullable();
            $table->text('capitulos')->nullable();
            $table->text('articulos')->nullable();
            $table->text('congresos')->nullable();
            $table->text('patentes')->nullable();
            $table->text('intelectuales')->nullable();
            $table->text('informes')->nullable();
            $table->boolean('congreso')->default(false);
            $table->text('tesis')->nullable();
            $table->text('tesinas')->nullable();
            $table->boolean('nacional')->default(false);
            $table->text('becas')->nullable();
            $table->text('objetivosC')->nullable();
            $table->text('planC')->nullable();
            $table->text('relacionProyectoC')->nullable();
            $table->text('aportesC')->nullable();
            $table->text('actividadesC')->nullable();
            $table->text('generalB')->nullable();
            $table->text('especificoB')->nullable();
            $table->text('actividadesB')->nullable();
            $table->text('cronogramaB')->nullable();
            $table->text('aportesB')->nullable();
            $table->text('relevanciaB')->nullable();
            $table->text('relevanciaA')->nullable();
            $table->string('scholar', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->decimal('puntaje', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->text('justificacion')->nullable();
            $table->string('disciplina', 255)->nullable();
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
        Schema::dropIfExists('viajes');
    }
}
