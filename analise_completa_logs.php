<?php

/**
 * Análise completa de todos os logs de kill automático
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProcessoLog;
use App\Models\Parametro;

echo "\n";
echo "=======================================================\n";
echo "  ANÁLISE COMPLETA - LOGS DE KILL AUTOMÁTICO\n";
echo "=======================================================\n";
echo "\n";

$parametros = Parametro::getParametros();

echo "Tempo Z configurado: {$parametros->tempo_kill_segundos_total} segundos (15 minutos)\n";
echo "\n";

// Função de conversão CORRIGIDA
function converterTempoParaSegundos($tempo)
{
    if (empty($tempo)) return 0;

    // Formato esperado: dd hh:mm:ss.mss (com ESPAÇO entre dias e horas)
    // Exemplo: "00 00:00:42.157" = 42 segundos
    if (preg_match('/^(\d+)\s+(\d+):(\d+):(\d+)\.(\d+)$/', $tempo, $matches)) {
        $dias = (int)$matches[1];
        $horas = (int)$matches[2];
        $minutos = (int)$matches[3];
        $segundos = (int)$matches[4];
        // milissegundos ignorados para o cálculo

        return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
    }

    // Fallback: tentar formato alternativo dd:hh:mm:ss.mss (sem espaço)
    $partes = explode(':', $tempo);
    if (count($partes) >= 4) {
        $dias = (int)$partes[0];
        $horas = (int)$partes[1];
        $minutos = (int)$partes[2];

        $segundosPartes = explode('.', $partes[3]);
        $segundos = (int)$segundosPartes[0];

        return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
    }

    return 0;
}

// Analisar o formato real do tempo
function analisarFormatoTempo($tempo) {
    echo "\n  📝 Análise detalhada do tempo: '{$tempo}'\n";

    // Verificar se tem espaço (formato: dd hh:mm:ss.mss)
    if (preg_match('/^(\d+)\s+(\d+):(\d+):(\d+)\.(\d+)$/', $tempo, $matches)) {
        echo "  ✓ Formato detectado: dd hh:mm:ss.mss (com espaço)\n";
        echo "    - Dias: {$matches[1]}\n";
        echo "    - Horas: {$matches[2]}\n";
        echo "    - Minutos: {$matches[3]}\n";
        echo "    - Segundos: {$matches[4]}\n";
        echo "    - Milissegundos: {$matches[5]}\n";

        $segundosCorretos = ($matches[1] * 24 * 60 * 60) +
                           ($matches[2] * 60 * 60) +
                           ($matches[3] * 60) +
                           $matches[4];

        return $segundosCorretos;
    }

    return null;
}

// Buscar TODOS os logs de kill automático
$totalLogs = ProcessoLog::where('tipo_kill', 'automatico')->count();

echo "Total de logs de kill automático: {$totalLogs}\n";
echo "\n";

$logsProblematicos = [];
$logsCorretos = [];

$todosLogs = ProcessoLog::where('tipo_kill', 'automatico')
    ->orderBy('killed_at', 'desc')
    ->get();

foreach ($todosLogs as $log) {
    $tempoSegundos = converterTempoParaSegundos($log->dd_hh_mm_ss_mss ?? '');

    // Analisar formato correto
    $segundosCorretos = analisarFormatoTempo($log->dd_hh_mm_ss_mss ?? '');

    if ($segundosCorretos === null) {
        $segundosCorretos = $tempoSegundos;
    }

    $tempoMinutos = $segundosCorretos / 60;

    $info = [
        'id' => $log->id,
        'session_id' => $log->session_id,
        'tempo_string' => $log->dd_hh_mm_ss_mss,
        'segundos_calculados_funcao_atual' => $tempoSegundos,
        'segundos_corretos' => $segundosCorretos,
        'minutos_corretos' => $tempoMinutos,
        'killed_at' => $log->killed_at,
    ];

    if ($segundosCorretos < 900) {
        $logsProblematicos[] = $info;
    } else {
        $logsCorretos[] = $info;
    }
}

echo "\n";
echo "=======================================================\n";
echo "  RESULTADO DA ANÁLISE\n";
echo "=======================================================\n";
echo "\n";

if (count($logsProblematicos) > 0) {
    echo "🚨 PROBLEMA ENCONTRADO!\n";
    echo "\n";
    echo "Processos finalizados com MENOS de 15 minutos:\n";
    echo "-------------------------------------------------------\n";

    foreach ($logsProblematicos as $info) {
        echo "\n❌ Log ID: {$info['id']} | Session ID: {$info['session_id']}\n";
        echo "   Tempo: {$info['tempo_string']}\n";
        echo "   Calculado pela função atual: {$info['segundos_calculados_funcao_atual']}s ({$info['minutos_corretos']} min)\n";
        echo "   Tempo correto: {$info['segundos_corretos']}s (" . round($info['minutos_corretos'], 2) . " min)\n";
        echo "   Finalizado em: {$info['killed_at']}\n";
        echo "   ⚠️  Este processo tinha MENOS de 15 minutos!\n";
    }

    echo "\n-------------------------------------------------------\n";
    echo "Total de processos finalizados incorretamente: " . count($logsProblematicos) . "\n";

} else {
    echo "✅ NENHUM PROBLEMA ENCONTRADO!\n";
    echo "\n";
    echo "Todos os {$totalLogs} processos finalizados automaticamente\n";
    echo "tinham tempo >= 15 minutos (900 segundos).\n";
}

echo "\n";
echo "=======================================================\n";
echo "\n";
