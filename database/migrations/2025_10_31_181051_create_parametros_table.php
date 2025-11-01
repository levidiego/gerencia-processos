<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->integer('tempo_destaque_minutos')->default(5)->comment('X - Tempo em minutos para destaque laranja');
            $table->integer('tempo_alerta_minutos')->default(10)->comment('Y - Tempo em minutos para alerta sonoro');
            $table->integer('tempo_kill_minutos')->default(15)->comment('Z - Tempo em minutos para kill automático');
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
        Schema::dropIfExists('parametros');
    }
}
