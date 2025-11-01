@extends('layouts.app')

@section('title', 'Processos SQL Server')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="text-white"><i class="bi bi-cpu"></i> Processos Ativos</h2>
            <p class="text-white-50">Monitoramento em tempo real do SQL Server</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-light" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Atualizar
            </button>
            <a href="{{ route('processos.index', ['bloqueados' => !$filtrarBloqueados]) }}" class="btn {{ $filtrarBloqueados ? 'btn-warning' : 'btn-outline-light' }}">
                <i class="bi bi-funnel"></i> {{ $filtrarBloqueados ? 'Mostrar Todos' : 'Apenas Bloqueados' }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul"></i> Lista de Processos</span>
                    <span class="badge bg-light text-dark">{{ count($processos) }} processos</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Session ID</th>
                                    <th style="width: 100px;">Tempo</th>
                                    <th style="width: 120px;">Login</th>
                                    <th style="width: 120px;">Host</th>
                                    <th style="width: 100px;">Database</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 100px;">Bloqueando</th>
                                    <th style="width: 80px;">Reads</th>
                                    <th style="width: 80px;">Writes</th>
                                    <th style="width: 80px;">CPU</th>
                                    <th>SQL Text</th>
                                    <th style="width: 80px;">Wait Info</th>
                                    <th style="width: 100px;">Program</th>
                                    <th style="width: 100px;" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($processos as $processo)
                                    <tr class="
                                        @if($processo['kill_automatico']) alerta-kill
                                        @elseif($processo['destacar_session']) destaque-session
                                        @elseif($processo['destacar_blocking']) destaque-blocking
                                        @endif
                                    ">
                                        <td class="fw-bold">{{ $processo['session_id'] ?? '' }}</td>
                                        <td>
                                            <small>{{ $processo['dd_hh_mm_ss_mss'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $processo['login_name'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $processo['host_name'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $processo['database_name'] ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $processo['status'] == 'running' ? 'success' : 'secondary' }}">
                                                {{ $processo['status'] ?? '' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(!empty($processo['blocking_session_id']))
                                                <span class="badge bg-danger">{{ $processo['blocking_session_id'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><small>{{ number_format((int)($processo['reads'] ?? 0)) }}</small></td>
                                        <td><small>{{ number_format((int)($processo['writes'] ?? 0)) }}</small></td>
                                        <td><small>{{ (int)($processo['CPU'] ?? 0) }} ms</small></td>
                                        <td>
                                            <small class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $processo['sql_text'] ?? '' }}">
                                                {{ $processo['sql_text'] ?? '' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if(!empty($processo['wait_info']))
                                                <small class="text-warning" title="{{ $processo['wait_info'] }}">
                                                    <i class="bi bi-hourglass-split"></i> Aguardando
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $processo['program_name'] ?? '' }}">
                                                {{ $processo['program_name'] ?? '' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('processos.kill') }}" style="display: inline;"
                                                  onsubmit="return confirm('Tem certeza que deseja finalizar o processo {{ $processo['session_id'] }}?')">
                                                @csrf
                                                <input type="hidden" name="session_id" value="{{ $processo['session_id'] }}">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Finalizar Processo">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Nenhum processo encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle"></i> Legenda</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <span class="badge destaque-session">Laranja</span>
                            <small>Bloqueador há mais de {{ (int)$parametros->tempo_destaque_minutos }}:{{ str_pad((int)($parametros->tempo_destaque_segundos ?? 0), 2, '0', STR_PAD_LEFT) }}</small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <span class="badge destaque-blocking">Amarelo</span>
                            <small>Bloqueador não encontrado</small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <span class="badge alerta-kill">Vermelho</span>
                            <small>Kill automático ({{ (int)$parametros->tempo_kill_minutos }}:{{ str_pad((int)($parametros->tempo_kill_segundos ?? 0), 2, '0', STR_PAD_LEFT) }})</small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <i class="bi bi-volume-up-fill text-warning"></i>
                            <small>Alerta sonoro em {{ (int)$parametros->tempo_alerta_minutos }}:{{ str_pad((int)($parametros->tempo_alerta_segundos ?? 0), 2, '0', STR_PAD_LEFT) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh a cada 30 segundos
    setTimeout(function() {
        location.reload();
    }, 30000);

    // Verificar alertas a cada 10 segundos
    let alertaSonoro = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVKzn77BeFg1Ln+LwumwhBzWN0/PQeCkGKn3M8N6MOQkZbr/u56JQEQ9Ppubi7WkfCz2W3PPKdSgGMI3R8tN9LQYmfMzw34k9CAxa');

    function verificarAlertas() {
        fetch('{{ route("processos.verificar-alertas") }}')
            .then(response => response.json())
            .then(data => {
                if (data.alertar) {
                    alertaSonoro.play().catch(e => console.log('Erro ao tocar som:', e));
                }
            })
            .catch(error => console.error('Erro ao verificar alertas:', error));
    }

    // Verificar alertas imediatamente e depois a cada 10 segundos
    verificarAlertas();
    setInterval(verificarAlertas, 10000);
</script>
@endsection
