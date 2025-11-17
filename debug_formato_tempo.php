<?php

/**
 * Debug do formato de tempo
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProcessoLog;

echo "\n";
echo "=======================================================\n";
echo "  DEBUG - FORMATO DE TEMPO\n";
echo "=======================================================\n";
echo "\n";

// Função atual (que está no código)
function converterTempoParaSegundosAtual($tempo)
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

        echo "  Debug conversão:\n";
        echo "  - partes: " . json_encode($partes) . "\n";
        echo "  - dias: {$dias}\n";
        echo "  - horas: {$horas}\n";
        echo "  - minutos: {$minutos}\n";
        echo "  - segundos: {$segundos}\n";

        return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
    }

    return 0;
}

// Pegar alguns logs para analisar
$logs = ProcessoLog::where('tipo_kill', 'automatico')
    ->orderBy('killed_at', 'desc')
    ->limit(5)
    ->get();

foreach ($logs as $log) {
    echo "\nAnalisando log {$log->id}:\n";
    echo "-------------------------------------------------------\n";
    echo "Tempo no banco: '{$log->dd_hh_mm_ss_mss}'\n";
    echo "Formato detectado:\n";

    $tempo = $log->dd_hh_mm_ss_mss;

    // Verificar se há espaço (formato: dd hh:mm:ss.mss)
    if (strpos($tempo, ' ') !== false) {
        echo "  ⚠️  Contém ESPAÇO (formato: dd hh:mm:ss.mss)\n";
    } else {
        echo "  ✓ Sem espaço (formato: dd:hh:mm:ss.mss)\n";
    }

    $segundosCalculados = converterTempoParaSegundosAtual($tempo);
    $minutosCalculados = $segundosCalculados / 60;

    echo "Resultado:\n";
    echo "  - Segundos: {$segundosCalculados}\n";
    echo "  - Minutos: {$minutosCalculados}\n";
    echo "-------------------------------------------------------\n";
}

echo "\n";
echo "=======================================================\n";
echo "\n";
