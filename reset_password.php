<?php

/**
 * Script para resetar senha de usuário
 *
 * Uso: php reset_password.php email@exemplo.com novaSenha
 */

if ($argc < 3) {
    echo "\n";
    echo "Uso: php reset_password.php email@exemplo.com novaSenha\n";
    echo "\n";
    echo "Exemplo: php reset_password.php admin@teste.com Admin@123\n";
    echo "\n";
    exit(1);
}

// Carregar o autoloader do Laravel
require __DIR__.'/vendor/autoload.php';

// Carregar a aplicação Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = $argv[1];
$novaSenha = $argv[2];

echo "\n";
echo "=======================================================\n";
echo "  RESETAR SENHA DE USUÁRIO\n";
echo "=======================================================\n";
echo "\n";

// Validar senha
if (strlen($novaSenha) < 8) {
    echo "❌ A senha deve ter no mínimo 8 caracteres!\n";
    echo "\n";
    exit(1);
}

// Buscar usuário pelo email
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ Usuário não encontrado: {$email}\n";
    echo "\n";
    echo "Usuários disponíveis:\n";
    $users = User::all();
    foreach ($users as $u) {
        $perfil = $u->is_admin ? 'Administrador' : 'Usuário';
        echo "  - {$u->email} ({$u->name}) - {$perfil}\n";
    }
    echo "\n";
    exit(1);
}

// Atualizar senha
$user->password = Hash::make($novaSenha);
$user->save();

echo "✅ Senha resetada com sucesso!\n";
echo "-------------------------------------------------------\n";
echo "Usuário: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Perfil: " . ($user->is_admin ? 'Administrador' : 'Usuário') . "\n";
echo "Nova senha: {$novaSenha}\n";
echo "-------------------------------------------------------\n";
echo "\n";
echo "⚠️  IMPORTANTE: Anote esta senha em local seguro!\n";
echo "\n";
