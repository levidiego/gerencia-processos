<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracaoTemaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracao_tema', function (Blueprint $table) {
            $table->id();
            $table->string('cor_primaria')->default('#667eea')->comment('Cor primária do gradiente');
            $table->string('cor_secundaria')->default('#764ba2')->comment('Cor secundária do gradiente');
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
        Schema::dropIfExists('configuracao_tema');
    }
}
