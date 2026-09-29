<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpidToUnidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidads', function (Blueprint $table) {
            // Réplica de unidad.bl_upid del origen (SICADI). 1 = UPID.
            // La usa el cálculo de subsidios para Ord (UPID aprobada -> 2).
            $table->tinyInteger('upid')->default(0)->after('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidads', function (Blueprint $table) {
            $table->dropColumn('upid');
        });
    }
}
