@extends('layouts.app')

@section('title', 'Dashboard - Gerência de Processos')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white"><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <p class="text-white-50">Bem-vindo ao sistema de gerenciamento de processos SQL Server</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cpu text-primary" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Processos Ativos</h5>
                    <p class="card-text">Monitore e gerencie processos do SQL Server em tempo real</p>
                    <a href="{{ route('processos.index') }}" class="btn btn-primary">
                        <i class="bi bi-eye"></i> Visualizar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people text-success" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Usuários</h5>
                    <p class="card-text">Gerencie usuários do sistema e suas permissões</p>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-success">
                        <i class="bi bi-person-gear"></i> Gerenciar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-gear text-info" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Parâmetros</h5>
                    <p class="card-text">Configure tempos de destaque, alerta e kill automático</p>
                    <a href="{{ route('parametros.index') }}" class="btn btn-info text-white">
                        <i class="bi bi-sliders"></i> Configurar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-key text-warning" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3">Segurança</h5>
                    <p class="card-text">Altere sua senha de acesso ao sistema</p>
                    <a href="{{ route('senha.edit') }}" class="btn btn-warning">
                        <i class="bi bi-shield-lock"></i> Trocar Senha
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Sobre o Sistema
                </div>
                <div class="card-body">
                    <h5>Sistema de Gerenciamento de Processos SQL Server</h5>
                    <p class="mb-2">Este sistema permite:</p>
                    <ul>
                        <li>Monitorar processos ativos do SQL Server via sp_whoisactive2</li>
                        <li>Identificar e destacar processos bloqueadores</li>
                        <li>Filtrar processos bloqueados</li>
                        <li>Executar KILL em processos específicos</li>
                        <li>Configurar alertas sonoros para processos de longa duração</li>
                        <li>Kill automático baseado em tempo configurável</li>
                    </ul>

                    <div class="alert alert-info mt-3">
                        <strong><i class="bi bi-palette"></i> Legendas de Cores:</strong><br>
                        <span class="badge" style="background-color: #ff9800;">Laranja</span> - Processo bloqueador acima do tempo X<br>
                        <span class="badge" style="background-color: #ffeb3b; color: #000;">Amarelo</span> - Bloqueador não encontrado na lista<br>
                        <span class="badge" style="background-color: #f44336;">Vermelho</span> - Processo marcado para kill automático
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
