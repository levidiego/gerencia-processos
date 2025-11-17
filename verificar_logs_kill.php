<?php

/**
 * Script para verificar logs de kill automático
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProcessoLog;
use App\Models\Parametro;

echo "\n";
echo "=======================================================\n";
echo "  VERIFICAÇÃO DE LOGS DE KILL AUTOMÁTICO\n";
echo "=======================================================\n";
echo "\n";

$parametros = Parametro::getParametros();

echo "Tempo Z configurado: {$parametros->tempo_kill_segundos_total} segundos (15 minutos)\n";
echo "\n";

// Função de conversão
function converterTempoParaSegundos($tempo)
{
    if (empty($tempo)) return 0;

    $partes = explode(':', $tempo);

    if (count($partes) >= 3) {
        $dias = isset($partes[0]) ? (int)$partes[0] : 0;
        $horas = isset($partes[1]) ? (int)$partes[1] : 0;

        $minutosSegundos = isset($partes[2]) ? $partes[2] : '0';
        $minutosSegundosPartes = explode('.', $minutosSegundos);
        $minutos = isset($minutosSegundosPartes[0]) ? (int)$minutosSegundosPartes[0] : 0;

        $segundos = 0;
        if (isset($partes[3])) {
            $segundosPartes = explode('.', $partes[3]);
            $segundos = isset($segundosPartes[0]) ? (int)$segundosPartes[0] : 0;
        }

        return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
    }

    return 0;
}

// Buscar logs de kill automático
$logsAutomaticos = ProcessoLog::where('tipo_kill', 'automatico')
    ->orderBy('killed_at', 'desc')
    ->limit(20)
    ->get();

if ($logsAutomaticos->isEmpty()) {
    echo "⚠️  Nenhum log de kill automático encontrado.\n";
    echo "\n";
} else {
    echo "Últimos 20 processos finalizados automaticamente:\n";
    echo "-------------------------------------------------------\n";

    $problemasEncontrados = 0;

    foreach ($logsAutomaticos as $log) {
        $tempoSegundos = converterTempoParaSegundos($log->dd_hh_mm_ss_mss ?? '');
        $tempoMinutos = $tempoSegundos / 60;

        $problema = $tempoSegundos < $parametros->tempo_kill_segundos_total;

        if ($problema) {
            $problemasEncontrados++;
            echo "\n❌ PROBLEMA DETECTADO!\n";
        } else {
            echo "\n✅ OK\n";
        }

        echo "Session ID: {$log->session_id}\n";
        echo "Tempo: {$log->dd_hh_mm_ss_mss}\n";
        echo "Calculado: {$tempoMinutos} minutos ({$tempoSegundos} segundos)\n";
        echo "Finalizado em: {$log->killed_at}\n";

        if ($problema) {
            echo "⚠️  Este processo foi finalizado com MENOS de 15 minutos!\n";
        }
    }

    echo "-------------------------------------------------------\n";
    echo "\n";

    if ($problemasEncontrados > 0) {
        echo "🚨 ATENÇÃO: {$problemasEncontrados} processo(s) foram finalizados incorretamente!\n";
        echo "   Eles tinham MENOS de 15 minutos de execução.\n";
    } else {
        echo "✅ Todos os processos finalizados automaticamente tinham >= 15 minutos.\n";
        echo "   A lógica está funcionando corretamente.\n";
    }
}

echo "\n";
echo "=======================================================\n";
echo "\n";
