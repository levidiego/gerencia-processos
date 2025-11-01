<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('processos.index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rotas que exigem apenas autenticação
Route::middleware('auth')->group(function () {
    // Rotas de Processos (acessível por todos os usuários autenticados)
    Route::get('/processos', [App\Http\Controllers\ProcessosController::class, 'index'])->name('processos.index');
    Route::post('/processos/kill', [App\Http\Controllers\ProcessosController::class, 'kill'])->name('processos.kill');
    Route::get('/processos/verificar-alertas', [App\Http\Controllers\ProcessosController::class, 'verificarAlertas'])->name('processos.verificar-alertas');

    // Rotas de Senha (acessível por todos os usuários autenticados)
    Route::get('/senha/editar', [App\Http\Controllers\UsuariosController::class, 'editSenha'])->name('senha.edit');
    Route::put('/senha', [App\Http\Controllers\UsuariosController::class, 'updateSenha'])->name('senha.update');
});

// Rotas que exigem permissão de administrador
Route::middleware(['auth', 'is_admin'])->group(function () {
    // Rotas de Parametros (apenas admin)
    Route::get('/parametros', [App\Http\Controllers\ParametrosController::class, 'index'])->name('parametros.index');
    Route::get('/parametros/editar', [App\Http\Controllers\ParametrosController::class, 'edit'])->name('parametros.edit');
    Route::put('/parametros', [App\Http\Controllers\ParametrosController::class, 'update'])->name('parametros.update');

    // Rotas de Logs (apenas admin)
    Route::get('/logs', [App\Http\Controllers\LogsController::class, 'index'])->name('logs.index');

    // Rotas de Usuários (apenas admin)
    Route::resource('usuarios', App\Http\Controllers\UsuariosController::class);

    // Rotas de Tema (apenas admin)
    Route::get('/tema/editar', [App\Http\Controllers\TemaController::class, 'edit'])->name('tema.edit');
    Route::put('/tema', [App\Http\Controllers\TemaController::class, 'update'])->name('tema.update');
});
