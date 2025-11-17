<?php

/**
 * Script de teste para verificar conversão de tempo
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro;

echo "\n";
echo "=======================================================\n";
echo "  TESTE DE CONVERSÃO DE TEMPO\n";
echo "=======================================================\n";
echo "\n";

$parametros = Parametro::getParametros();

echo "Tempo Z (Kill Automático): {$parametros->tempo_kill_segundos_total} segundos (15 minutos)\n";
echo "\n";

// Função de conversão (copiada do KillProcessosAutomatico)
function converterTempoParaSegundos($tempo)
{
    if (empty($tempo)) return 0;

    // Formato esperado: dd:hh:mm:ss.mss
    $partes = explode(':', $tempo);

    if (count($partes) >= 3) {
        $dias = isset($partes[0]) ? (int)$partes[0] : 0;
        $horas = isset($partes[1]) ? (int)$partes[1] : 0;

        // Separa minutos e segundos
        $minutosSegundos = isset($partes[2]) ? $partes[2] : '0';
        $minutosSegundosPartes = explode('.', $minutosSegundos);
        $minutos = isset($minutosSegundosPartes[0]) ? (int)$minutosSegundosPartes[0] : 0;

        // Segundos (se existir uma quarta parte ou decimal)
        $segundos = 0;
        if (isset($partes[3])) {
            $segundosPartes = explode('.', $partes[3]);
            $segundos = isset($segundosPartes[0]) ? (int)$segundosPartes[0] : 0;
        }

        return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
    }

    return 0;
}

echo "Testando conversão de tempos:\n";
echo "-------------------------------------------------------\n";

$testeCasos = [
    '00:00:05:00.000' => 5,     // 5 minutos
    '00:00:10:00.000' => 10,    // 10 minutos
    '00:00:14:00.000' => 14,    // 14 minutos (menor que 15)
    '00:00:14:59.000' => 14.98, // 14m59s (menor que 15)
    '00:00:15:00.000' => 15,    // 15 minutos (igual)
    '00:00:16:00.000' => 16,    // 16 minutos (maior que 15)
    '00:00:20:00.000' => 20,    // 20 minutos
];

foreach ($testeCasos as $tempo => $minutosEsperados) {
    $segundosCalculados = converterTempoParaSegundos($tempo);
    $minutosCalculados = $segundosCalculados / 60;
    $deveSerFinalizado = $segundosCalculados >= $parametros->tempo_kill_segundos_total ? 'SIM' : 'NÃO';

    echo sprintf(
        "Tempo: %s | Esperado: %.2f min (%d s) | Calculado: %.2f min (%d s) | Kill? %s\n",
        $tempo,
        $minutosEsperados,
        (int)($minutosEsperados * 60),
        $minutosCalculados,
        $segundosCalculados,
        $deveSerFinalizado
    );
}

echo "-------------------------------------------------------\n";
echo "\n";

echo "Verificando lógica de comparação:\n";
echo "-------------------------------------------------------\n";
echo "Um processo será finalizado se: tempo >= {$parametros->tempo_kill_segundos_total}s\n";
echo "Ou seja, processos com MENOS de 15 minutos NÃO devem ser finalizados.\n";
echo "-------------------------------------------------------\n";
echo "\n";
