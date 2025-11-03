<?php

/**
 * Script para listar usuários do sistema
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "\n";
echo "=======================================================\n";
echo "  USUÁRIOS CADASTRADOS NO SISTEMA\n";
echo "=======================================================\n";
echo "\n";

$users = User::all();

if ($users->isEmpty()) {
    echo "⚠️  Nenhum usuário cadastrado!\n";
    echo "\n";
    echo "Crie o primeiro usuário acessando:\n";
    echo "http://192.168.254.185:8001/register\n";
    echo "\n";
    echo "Depois, torne-o administrador:\n";
    echo "php reset_password.php email@exemplo.com NovaSenha123\n";
    echo "\n";
} else {
    echo "Total de usuários: " . $users->count() . "\n";
    echo "-------------------------------------------------------\n";

    foreach ($users as $user) {
        $perfil = $user->is_admin ? 'ADMINISTRADOR' : 'Usuário';
        $icone = $user->is_admin ? '👑' : '👤';

        echo "\n";
        echo "{$icone} {$user->name}\n";
        echo "   Email: {$user->email}\n";
        echo "   Perfil: {$perfil}\n";
        echo "   Criado em: {$user->created_at}\n";
    }

    echo "\n";
    echo "-------------------------------------------------------\n";
}

echo "\n";
echo "Para resetar senha de algum usuário:\n";
echo "php reset_password.php email@exemplo.com NovaSenha123\n";
echo "\n";
echo "=======================================================\n";
echo "\n";
