<?php

/**
 * Script para criar usuário administrador
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n";
echo "=======================================================\n";
echo "  CRIAR USUÁRIO ADMINISTRADOR\n";
echo "=======================================================\n";
echo "\n";

$nome = $argv[1] ?? 'Administrador';
$email = $argv[2] ?? 'admin@teste.com';
$senha = $argv[3] ?? 'Admin@123';

// Verificar se o email já existe
$existente = User::where('email', $email)->first();

if ($existente) {
    echo "⚠️  Usuário com este email já existe!\n";
    echo "\n";
    echo "Email: {$existente->email}\n";
    echo "Nome: {$existente->name}\n";
    echo "Perfil: " . ($existente->is_admin ? 'Administrador' : 'Usuário') . "\n";
    echo "\n";
    echo "Para resetar a senha, use:\n";
    echo "php reset_password.php {$email} NovaSenha123\n";
    echo "\n";
    exit(1);
}

// Criar o usuário
$user = User::create([
    'name' => $nome,
    'email' => $email,
    'password' => Hash::make($senha),
    'is_admin' => true,
]);

echo "✅ Usuário administrador criado com sucesso!\n";
echo "-------------------------------------------------------\n";
echo "Nome: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Senha: {$senha}\n";
echo "Perfil: Administrador\n";
echo "-------------------------------------------------------\n";
echo "\n";
echo "Acesse o sistema:\n";
echo "http://192.168.254.185:8001/login\n";
echo "\n";
echo "⚠️  IMPORTANTE: Anote estas credenciais!\n";
echo "\n";
echo "=======================================================\n";
echo "\n";
