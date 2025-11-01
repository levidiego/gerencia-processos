<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSegundosToParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->integer('tempo_destaque_segundos')->default(0)->after('tempo_destaque_minutos')->comment('Segundos adicionais para destaque');
            $table->integer('tempo_alerta_segundos')->default(0)->after('tempo_alerta_minutos')->comment('Segundos adicionais para alerta');
            $table->integer('tempo_kill_segundos')->default(0)->after('tempo_kill_minutos')->comment('Segundos adicionais para kill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn(['tempo_destaque_segundos', 'tempo_alerta_segundos', 'tempo_kill_segundos']);
        });
    }
}
