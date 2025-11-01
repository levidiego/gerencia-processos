<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Criando usuário de teste...\n\n";

try {
    // Verifica se já existe
    $existingUser = User::where('email', 'admin@teste.com')->first();

    if ($existingUser) {
        echo "Usuário já existe!\n";
        echo "Email: admin@teste.com\n";
        echo "Senha: senha123\n";
        exit;
    }

    // Cria o usuário
    $user = User::create([
        'name' => 'Administrador',
        'email' => 'admin@teste.com',
        'password' => Hash::make('senha123'),
    ]);

    echo "✓ Usuário criado com sucesso!\n\n";
    echo "Credenciais de acesso:\n";
    echo "======================\n";
    echo "Email: admin@teste.com\n";
    echo "Senha: senha123\n\n";

} catch (Exception $e) {
    echo "✗ Erro ao criar usuário: " . $e->getMessage() . "\n";
}
