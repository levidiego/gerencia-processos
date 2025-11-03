<?php

/**
 * Script para resetar a senha do usuário administrador
 *
 * Uso: php reset_admin_password.php
 */

// Carregar o autoloader do Laravel
require __DIR__.'/vendor/autoload.php';

// Carregar a aplicação Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n";
echo "=======================================================\n";
echo "  RESETAR SENHA DO ADMINISTRADOR\n";
echo "=======================================================\n";
echo "\n";

// Buscar todos os usuários administradores
$admins = User::where('is_admin', 1)->get();

if ($admins->isEmpty()) {
    echo "❌ Nenhum usuário administrador encontrado!\n";
    echo "\n";
    echo "Para tornar um usuário em administrador, execute:\n";
    echo "UPDATE users SET is_admin = 1 WHERE email = 'seu-email@exemplo.com';\n";
    echo "\n";
    exit(1);
}

echo "Administradores encontrados:\n";
echo "-------------------------------------------------------\n";
foreach ($admins as $index => $admin) {
    echo ($index + 1) . ". {$admin->name} ({$admin->email})\n";
}
echo "-------------------------------------------------------\n";
echo "\n";

// Se houver apenas um admin, seleciona automaticamente
if ($admins->count() == 1) {
    $selectedAdmin = $admins->first();
    echo "Usuário selecionado: {$selectedAdmin->name} ({$selectedAdmin->email})\n";
} else {
    // Solicitar qual usuário resetar
    echo "Digite o número do usuário para resetar a senha: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $userNumber = (int) trim($line);
    fclose($handle);

    if ($userNumber < 1 || $userNumber > $admins->count()) {
        echo "❌ Número inválido!\n";
        exit(1);
    }

    $selectedAdmin = $admins[$userNumber - 1];
}

echo "\n";

// Solicitar nova senha
echo "Digite a nova senha (mínimo 8 caracteres): ";
$handle = fopen("php://stdin", "r");
$novaSenha = trim(fgets($handle));
fclose($handle);

if (strlen($novaSenha) < 8) {
    echo "❌ A senha deve ter no mínimo 8 caracteres!\n";
    exit(1);
}

// Confirmar senha
echo "Confirme a nova senha: ";
$handle = fopen("php://stdin", "r");
$confirmaSenha = trim(fgets($handle));
fclose($handle);

if ($novaSenha !== $confirmaSenha) {
    echo "❌ As senhas não conferem!\n";
    exit(1);
}

// Atualizar senha
$selectedAdmin->password = Hash::make($novaSenha);
$selectedAdmin->save();

echo "\n";
echo "=======================================================\n";
echo "✅ Senha resetada com sucesso!\n";
echo "=======================================================\n";
echo "\n";
echo "Usuário: {$selectedAdmin->name}\n";
echo "Email: {$selectedAdmin->email}\n";
echo "Nova senha: {$novaSenha}\n";
echo "\n";
echo "⚠️  IMPORTANTE: Anote esta senha em local seguro!\n";
echo "\n";
