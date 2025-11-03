<?php

/**
 * Script para testar a conexão com o banco de dados
 */

// Carregar o autoloader do Laravel
require __DIR__.'/vendor/autoload.php';

// Carregar a aplicação Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "=======================================================\n";
echo "  TESTE DE CONEXÃO COM O BANCO DE DADOS\n";
echo "=======================================================\n";
echo "\n";

try {
    // Obter configurações
    $host = config('database.connections.sqlsrv.host');
    $database = config('database.connections.sqlsrv.database');
    $username = config('database.connections.sqlsrv.username');

    echo "Configurações:\n";
    echo "-------------------------------------------------------\n";
    echo "Host: {$host}\n";
    echo "Database: {$database}\n";
    echo "Username: {$username}\n";
    echo "-------------------------------------------------------\n";
    echo "\n";

    echo "Testando conexão...\n";

    // Testar conexão
    DB::connection()->getPdo();

    echo "✅ Conexão estabelecida com sucesso!\n";
    echo "\n";

    // Verificar versão do SQL Server
    $version = DB::select('SELECT @@VERSION AS version')[0]->version;
    echo "Versão do SQL Server:\n";
    echo "-------------------------------------------------------\n";
    echo $version . "\n";
    echo "-------------------------------------------------------\n";
    echo "\n";

    // Verificar se o banco existe
    $dbExists = DB::select("SELECT name FROM sys.databases WHERE name = ?", [$database]);

    if (!empty($dbExists)) {
        echo "✅ Banco de dados '{$database}' encontrado!\n";
        echo "\n";

        // Listar tabelas
        echo "Tabelas existentes:\n";
        echo "-------------------------------------------------------\n";
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME");

        if (empty($tables)) {
            echo "⚠️  Nenhuma tabela encontrada. Execute as migrations:\n";
            echo "   php artisan migrate\n";
        } else {
            foreach ($tables as $table) {
                echo "  - {$table->TABLE_NAME}\n";
            }
        }
        echo "-------------------------------------------------------\n";
        echo "\n";
    } else {
        echo "⚠️  Banco de dados '{$database}' não encontrado!\n";
        echo "\n";
        echo "Execute o script SQL de backup para criar o banco:\n";
        echo "instalacao/backup_banco_gerencia_processos.sql\n";
        echo "\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro ao conectar ao banco de dados!\n";
    echo "-------------------------------------------------------\n";
    echo "Erro: " . $e->getMessage() . "\n";
    echo "-------------------------------------------------------\n";
    echo "\n";
    echo "Verifique as configurações no arquivo .env:\n";
    echo "  - DB_HOST\n";
    echo "  - DB_DATABASE\n";
    echo "  - DB_USERNAME\n";
    echo "  - DB_PASSWORD\n";
    echo "\n";
    exit(1);
}

echo "=======================================================\n";
echo "\n";
