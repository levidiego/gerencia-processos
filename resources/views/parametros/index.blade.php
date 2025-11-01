@extends('layouts.app')

@section('title', 'Parâmetros do Sistema')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="text-white"><i class="bi bi-gear"></i> Parâmetros do Sistema</h2>
            <p class="text-white-50">Configure os tempos de destaque, alerta e kill automático</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('parametros.edit') }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Parâmetros
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-sliders"></i> Configurações Atuais
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning bg-opacity-10 border-warning">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                                    <h3 class="mt-3 mb-0">
                                        {{ $parametros->tempo_destaque_minutos }}:{{ str_pad($parametros->tempo_destaque_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}
                                    </h3>
                                    <p class="text-muted mb-0">min:seg</p>
                                    <p class="text-muted small">({{ $parametros->tempo_destaque_segundos_total ?? 0 }} segundos)</p>
                                    <hr>
                                    <h6 class="card-title">Tempo X - Destaque Laranja</h6>
                                    <p class="card-text small">
                                        Processos bloqueadores com tempo superior a este valor serão destacados em laranja.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card bg-info bg-opacity-10 border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-volume-up-fill text-info" style="font-size: 3rem;"></i>
                                    <h3 class="mt-3 mb-0">
                                        {{ $parametros->tempo_alerta_minutos }}:{{ str_pad($parametros->tempo_alerta_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}
                                    </h3>
                                    <p class="text-muted mb-0">min:seg</p>
                                    <p class="text-muted small">({{ $parametros->tempo_alerta_segundos_total ?? 0 }} segundos)</p>
                                    <hr>
                                    <h6 class="card-title">Tempo Y - Alerta Sonoro</h6>
                                    <p class="card-text small">
                                        Um alerta sonoro será emitido quando processos bloqueadores atingirem este tempo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card bg-danger bg-opacity-10 border-danger">
                                <div class="card-body text-center">
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                                    <h3 class="mt-3 mb-0">
                                        {{ $parametros->tempo_kill_minutos }}:{{ str_pad($parametros->tempo_kill_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}
                                    </h3>
                                    <p class="text-muted mb-0">min:seg</p>
                                    <p class="text-muted small">({{ $parametros->tempo_kill_segundos_total ?? 0 }} segundos)</p>
                                    <hr>
                                    <h6 class="card-title">Tempo Z - Kill Automático</h6>
                                    <p class="card-text small">
                                        Processos bloqueadores com tempo superior a este valor serão marcados para kill automático.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Como funciona?</h6>
                        <ul class="mb-0">
                            <li><strong>X (Destaque):</strong> Identifica visualmente processos bloqueadores de longa duração</li>
                            <li><strong>Y (Alerta):</strong> Notifica sonoramente quando a situação requer atenção</li>
                            <li><strong>Z (Kill):</strong> Marca processos críticos que excedem o tempo máximo aceitável</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Importante:</strong>
                        Os valores devem seguir a ordem: X &lt; Y &lt; Z. O sistema espera que o tempo de destaque seja menor que o de alerta,
                        e o de alerta menor que o de kill automático.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-palette"></i> Exemplo Visual
                </div>
                <div class="card-body">
                    <p class="mb-3">Veja como os processos são destacados de acordo com os tempos configurados:</p>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tempo do Processo</th>
                                    <th>Visualização</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Menos de {{ $parametros->tempo_destaque_minutos }}:{{ str_pad($parametros->tempo_destaque_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td><span class="badge bg-secondary">Normal</span></td>
                                    <td>Processo normal, sem destaque</td>
                                </tr>
                                <tr class="destaque-session">
                                    <td>{{ $parametros->tempo_destaque_minutos }}:{{ str_pad($parametros->tempo_destaque_segundos ?? 0, 2, '0', STR_PAD_LEFT) }} - {{ $parametros->tempo_alerta_minutos }}:{{ str_pad($parametros->tempo_alerta_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td><span class="badge" style="background-color: #ff9800;">Laranja</span></td>
                                    <td>Processo bloqueador com destaque</td>
                                </tr>
                                <tr class="destaque-session">
                                    <td>{{ $parametros->tempo_alerta_minutos }}:{{ str_pad($parametros->tempo_alerta_segundos ?? 0, 2, '0', STR_PAD_LEFT) }} - {{ $parametros->tempo_kill_minutos }}:{{ str_pad($parametros->tempo_kill_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td><span class="badge" style="background-color: #ff9800;">Laranja + Som</span></td>
                                    <td>Processo com alerta sonoro ativo</td>
                                </tr>
                                <tr class="alerta-kill">
                                    <td>{{ $parametros->tempo_kill_minutos }}:{{ str_pad($parametros->tempo_kill_segundos ?? 0, 2, '0', STR_PAD_LEFT) }}+</td>
                                    <td><span class="badge bg-danger">Vermelho Piscante</span></td>
                                    <td>Processo marcado para kill automático</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
