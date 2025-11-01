@extends('layouts.app')

@section('title', 'Logs de Processos Finalizados')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="text-white"><i class="bi bi-journal-text"></i> Logs de Processos Finalizados</h2>
            <p class="text-white-50">Histórico de processos finalizados (manual e automático)</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-funnel"></i> Filtros
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('logs.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Tipo de Kill</label>
                                <select name="tipo_kill" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="manual" {{ request('tipo_kill') == 'manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="automatico" {{ request('tipo_kill') == 'automatico' ? 'selected' : '' }}>Automático</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Session ID</label>
                                <input type="number" name="session_id" class="form-control" value="{{ request('session_id') }}" placeholder="ID da Sessão">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                                <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul"></i> Histórico de Processos</span>
                    <span class="badge bg-light text-dark">{{ $logs->total() }} registro(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Session ID</th>
                                    <th style="width: 150px;">Data/Hora Kill</th>
                                    <th style="width: 100px;">Tipo Kill</th>
                                    <th style="width: 120px;">Usuário</th>
                                    <th style="width: 100px;">Tempo Exec.</th>
                                    <th style="width: 120px;">Login</th>
                                    <th style="width: 120px;">Host</th>
                                    <th style="width: 100px;">Database</th>
                                    <th style="width: 100px;">Status</th>
                                    <th>SQL Text</th>
                                    <th style="width: 150px;">Program</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="fw-bold">{{ $log->session_id }}</td>
                                        <td>
                                            <small>{{ $log->killed_at->format('d/m/Y H:i:s') }}</small>
                                        </td>
                                        <td>
                                            @if($log->tipo_kill == 'manual')
                                                <span class="badge bg-info">Manual</span>
                                            @else
                                                <span class="badge bg-danger">Automático</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $log->usuario->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $log->dd_hh_mm_ss_mss ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $log->login_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $log->host_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $log->database_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $log->status ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $log->sql_text ?? '' }}">
                                                {{ $log->sql_text ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $log->program_name ?? '' }}">
                                                {{ $log->program_name ?? '-' }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Nenhum log encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informações Estatísticas -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="text-primary">{{ \App\Models\ProcessoLog::count() }}</h5>
                            <small class="text-muted">Total de Processos Finalizados</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-info">{{ \App\Models\ProcessoLog::where('tipo_kill', 'manual')->count() }}</h5>
                            <small class="text-muted">Kills Manuais</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-danger">{{ \App\Models\ProcessoLog::where('tipo_kill', 'automatico')->count() }}</h5>
                            <small class="text-muted">Kills Automáticos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
