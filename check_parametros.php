<?php

/**
 * Script para verificar parâmetros e testar lógica de destaque
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Parametro;

echo "\n";
echo "=======================================================\n";
echo "  VERIFICAÇÃO DE PARÂMETROS E LÓGICA\n";
echo "=======================================================\n";
echo "\n";

$parametros = Parametro::getParametros();

echo "Parâmetros Atuais:\n";
echo "-------------------------------------------------------\n";
echo "Tempo X (Destaque Laranja):\n";
echo "  - Minutos: {$parametros->tempo_destaque_minutos}\n";
echo "  - Segundos: {$parametros->tempo_destaque_segundos}\n";
echo "  - Total: {$parametros->tempo_destaque_segundos_total} segundos\n";
echo "\n";

echo "Tempo Y (Alerta Sonoro):\n";
echo "  - Minutos: {$parametros->tempo_alerta_minutos}\n";
echo "  - Segundos: {$parametros->tempo_alerta_segundos}\n";
echo "  - Total: {$parametros->tempo_alerta_segundos_total} segundos\n";
echo "\n";

echo "Tempo Z (Kill Automático):\n";
echo "  - Minutos: {$parametros->tempo_kill_minutos}\n";
echo "  - Segundos: {$parametros->tempo_kill_segundos}\n";
echo "  - Total: {$parametros->tempo_kill_segundos_total} segundos\n";
echo "-------------------------------------------------------\n";
echo "\n";

echo "Exemplos de Tempo para Teste:\n";
echo "-------------------------------------------------------\n";

$temposExemplo = [
    '00:00:04:00.000' => 'Não destacar',
    '00:00:05:00.000' => 'Destacar LARANJA (>= X)',
    '00:00:10:00.000' => 'ALERTA SONORO (>= Y)',
    '00:00:15:00.000' => 'KILL AUTOMÁTICO (>= Z)',
];

foreach ($temposExemplo as $tempo => $resultado) {
    echo "{$tempo} -> {$resultado}\n";
}

echo "-------------------------------------------------------\n";
echo "\n";

echo "Como funciona:\n";
echo "1. Um processo BLOQUEADOR é aquele que aparece como\n";
echo "   'blocking_session_id' em outros processos\n";
echo "2. Se o bloqueador estiver rodando há >= {$parametros->tempo_destaque_segundos_total}s:\n";
echo "   -> Linha fica LARANJA\n";
echo "3. Se >= {$parametros->tempo_alerta_segundos_total}s:\n";
echo "   -> Emite ALERTA SONORO\n";
echo "4. Se >= {$parametros->tempo_kill_segundos_total}s:\n";
echo "   -> Linha fica VERMELHA PISCANDO (kill automático)\n";
echo "\n";

echo "=======================================================\n";
echo "\n";
