<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJovensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jovens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investigador_id')->nullable();
            $table->foreign('investigador_id')->references('id')->on('investigadors');
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->foreign('periodo_id')->references('id')->on('periodos');
            $table->enum('estado', ['Creada','Recibida','Admitida','No Admitida','Otorgada-No-Rendida','En evaluación','No otorgada','Evaluada','Otorgada-Rendida','Otorgada-Renunciada','Retirada','Otorgada-Devuelta'])->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('notificacion')->default(true);
            $table->string('telefono', 30)->nullable();
            $table->datetime('fecha')->nullable();
            $table->datetime('nacimiento')->nullable();
            $table->string('calle', 50)->nullable();
            $table->string('nro', 10)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('depto', 10)->nullable();
            $table->string('cp', 10)->nullable();
            $table->unsignedBigInteger('titulo_id')->nullable();
            $table->foreign('titulo_id')->references('id')->on('titulos');
            $table->datetime('egresogrado')->nullable();
            $table->unsignedBigInteger('titulopost_id')->nullable();
            $table->foreign('titulopost_id')->references('id')->on('titulos');
            $table->datetime('egresoposgrado')->nullable();
            $table->boolean('doctorado')->default(true);
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->foreign('unidad_id')->references('id')->on('unidads');
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->foreign('cargo_id')->references('id')->on('cargos');
            $table->enum('deddoc', ['Exclusiva','Semi Exclusiva','Simple']);
            $table->unsignedBigInteger('facultad_id')->nullable();
            $table->foreign('facultad_id')->references('id')->on('facultads');
            $table->unsignedBigInteger('facultadplanilla_id')->nullable();
            $table->foreign('facultadplanilla_id')->references('id')->on('facultads');
            $table->boolean('director')->default(true);
            $table->unsignedBigInteger('carrerainv_id')->nullable();
            $table->foreign('carrerainv_id')->references('id')->on('carrerainvs');
            $table->unsignedBigInteger('organismo_id')->nullable();
            $table->foreign('organismo_id')->references('id')->on('organismos');
            $table->datetime('ingreso_carrerainv')->nullable();
            $table->unsignedBigInteger('unidadcarrera_id')->nullable();
            $table->foreign('unidadcarrera_id')->references('id')->on('unidads');
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
        Schema::dropIfExists('jovens');
    }
}
