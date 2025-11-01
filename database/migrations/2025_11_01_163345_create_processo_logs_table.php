<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processo_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('session_id')->comment('ID da sessão finalizada');
            $table->text('sql_text')->nullable()->comment('Comando SQL que estava sendo executado');
            $table->string('dd_hh_mm_ss_mss')->nullable()->comment('Tempo de execução do processo');
            $table->string('login_name')->nullable()->comment('Login do usuário');
            $table->string('status')->nullable()->comment('Status do processo');
            $table->string('host_name')->nullable()->comment('Nome do host');
            $table->string('database_name')->nullable()->comment('Nome do banco de dados');
            $table->string('program_name')->nullable()->comment('Nome do programa');
            $table->string('tipo_kill')->comment('Tipo de kill: manual ou automatico');
            $table->unsignedBigInteger('killed_by')->nullable()->comment('ID do usuário que executou o kill manual');
            $table->timestamp('killed_at')->useCurrent()->comment('Data e hora do kill');
            $table->timestamps();

            $table->foreign('killed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processo_logs');
    }
}
