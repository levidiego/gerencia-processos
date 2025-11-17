<?php

/**
 * Teste da função corrigida converterTempoParaSegundos
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro;

echo "\n";
echo "=======================================================\n";
echo "  TESTE DA FUNÇÃO CORRIGIDA\n";
echo "=======================================================\n";
echo "\n";

$parametros = Parametro::getParametros();

echo "Tempo Z (Kill Automático): {$parametros->tempo_kill_segundos_total} segundos (15 minutos)\n";
echo "\n";

// Função corrigida
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

echo "Testando conversão de tempos:\n";
echo "-------------------------------------------------------\n";

$testeCasos = [
    // Formato com espaço (real do SQL Server)
    '00 00:00:15.000' => ['esperado_seg' => 15, 'esperado_min' => 0.25, 'deve_kill' => false],
    '00 00:00:42.157' => ['esperado_seg' => 42, 'esperado_min' => 0.7, 'deve_kill' => false],
    '00 00:01:00.000' => ['esperado_seg' => 60, 'esperado_min' => 1, 'deve_kill' => false],
    '00 00:05:00.000' => ['esperado_seg' => 300, 'esperado_min' => 5, 'deve_kill' => false],
    '00 00:10:00.000' => ['esperado_seg' => 600, 'esperado_min' => 10, 'deve_kill' => false],
    '00 00:14:59.000' => ['esperado_seg' => 899, 'esperado_min' => 14.98, 'deve_kill' => false],
    '00 00:15:00.000' => ['esperado_seg' => 900, 'esperado_min' => 15, 'deve_kill' => true],
    '00 00:16:00.000' => ['esperado_seg' => 960, 'esperado_min' => 16, 'deve_kill' => true],
    '00 00:20:00.000' => ['esperado_seg' => 1200, 'esperado_min' => 20, 'deve_kill' => true],
    '00 01:00:00.000' => ['esperado_seg' => 3600, 'esperado_min' => 60, 'deve_kill' => true],
    '01 00:00:00.000' => ['esperado_seg' => 86400, 'esperado_min' => 1440, 'deve_kill' => true],

    // Formato sem espaço (fallback)
    '00:00:15:00.000' => ['esperado_seg' => 900, 'esperado_min' => 15, 'deve_kill' => true],
];

$totalTestes = count($testeCasos);
$testesPassaram = 0;
$testesFalharam = 0;

foreach ($testeCasos as $tempo => $esperado) {
    $segundosCalculados = converterTempoParaSegundos($tempo);
    $minutosCalculados = $segundosCalculados / 60;
    $deveSerFinalizado = $segundosCalculados >= $parametros->tempo_kill_segundos_total;

    $passou = ($segundosCalculados == $esperado['esperado_seg']) &&
              ($deveSerFinalizado == $esperado['deve_kill']);

    if ($passou) {
        echo "\n✅ PASSOU";
        $testesPassaram++;
    } else {
        echo "\n❌ FALHOU";
        $testesFalharam++;
    }

    echo "\n   Tempo: {$tempo}";
    echo "\n   Esperado: {$esperado['esperado_seg']}s (" . round($esperado['esperado_min'], 2) . " min)";
    echo "\n   Calculado: {$segundosCalculados}s (" . round($minutosCalculados, 2) . " min)";
    echo "\n   Kill? " . ($deveSerFinalizado ? 'SIM' : 'NÃO') . " (esperado: " . ($esperado['deve_kill'] ? 'SIM' : 'NÃO') . ")";

    if (!$passou) {
        echo "\n   ⚠️  ERRO: Valor calculado difere do esperado!";
    }
}

echo "\n\n-------------------------------------------------------\n";
echo "Resultado dos testes:\n";
echo "Total: {$totalTestes} | Passaram: {$testesPassaram} | Falharam: {$testesFalharam}\n";

if ($testesFalharam == 0) {
    echo "\n✅ TODOS OS TESTES PASSARAM!\n";
    echo "A função está funcionando corretamente.\n";
} else {
    echo "\n❌ ALGUNS TESTES FALHARAM!\n";
    echo "Há problemas na função de conversão.\n";
}

echo "-------------------------------------------------------\n";
echo "\n";
