@extends('layouts.app')

@section('title', 'Editar Parâmetros')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-white"><i class="bi bi-sliders"></i> Editar Parâmetros</h2>
            <p class="text-white-50">Configure os tempos de monitoramento do sistema</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Configurações de Tempo
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('parametros.update') }}" id="formParametros">
                        @csrf
                        @method('PUT')

                        <!-- Tempo X - Destaque -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-clock-history text-warning"></i>
                                <strong>Tempo X - Destaque Laranja</strong>
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_destaque_minutos') is-invalid @enderror"
                                               id="tempo_destaque_minutos" name="tempo_destaque_minutos"
                                               value="{{ old('tempo_destaque_minutos', $parametros->tempo_destaque_minutos ?? 0) }}"
                                               min="0" required>
                                        <span class="input-group-text">minutos</span>
                                        @error('tempo_destaque_minutos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_destaque_segundos') is-invalid @enderror"
                                               id="tempo_destaque_segundos" name="tempo_destaque_segundos"
                                               value="{{ old('tempo_destaque_segundos', $parametros->tempo_destaque_segundos ?? 0) }}"
                                               min="0" max="59" required>
                                        <span class="input-group-text">segundos</span>
                                        @error('tempo_destaque_segundos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                Processos bloqueadores com tempo superior a este valor serão destacados em laranja.
                            </small>
                        </div>

                        <!-- Tempo Y - Alerta -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-volume-up-fill text-info"></i>
                                <strong>Tempo Y - Alerta Sonoro</strong>
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_alerta_minutos') is-invalid @enderror"
                                               id="tempo_alerta_minutos" name="tempo_alerta_minutos"
                                               value="{{ old('tempo_alerta_minutos', $parametros->tempo_alerta_minutos ?? 0) }}"
                                               min="0" required>
                                        <span class="input-group-text">minutos</span>
                                        @error('tempo_alerta_minutos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_alerta_segundos') is-invalid @enderror"
                                               id="tempo_alerta_segundos" name="tempo_alerta_segundos"
                                               value="{{ old('tempo_alerta_segundos', $parametros->tempo_alerta_segundos ?? 0) }}"
                                               min="0" max="59" required>
                                        <span class="input-group-text">segundos</span>
                                        @error('tempo_alerta_segundos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                Um alerta sonoro será emitido quando processos bloqueadores atingirem este tempo.
                            </small>
                        </div>

                        <!-- Tempo Z - Kill -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-x-circle-fill text-danger"></i>
                                <strong>Tempo Z - Kill Automático</strong>
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_kill_minutos') is-invalid @enderror"
                                               id="tempo_kill_minutos" name="tempo_kill_minutos"
                                               value="{{ old('tempo_kill_minutos', $parametros->tempo_kill_minutos ?? 0) }}"
                                               min="0" required>
                                        <span class="input-group-text">minutos</span>
                                        @error('tempo_kill_minutos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tempo_kill_segundos') is-invalid @enderror"
                                               id="tempo_kill_segundos" name="tempo_kill_segundos"
                                               value="{{ old('tempo_kill_segundos', $parametros->tempo_kill_segundos ?? 0) }}"
                                               min="0" max="59" required>
                                        <span class="input-group-text">segundos</span>
                                        @error('tempo_kill_segundos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                Processos bloqueadores com tempo superior a este valor serão marcados para kill automático.
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong><br>
                            Os tempos totais devem seguir a ordem: <strong>X &lt; Y &lt; Z</strong><br>
                            Exemplo: X=5min 30seg, Y=10min 0seg, Z=15min 30seg
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Salvar Alterações
                            </button>
                            <a href="{{ route('parametros.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('formParametros').addEventListener('submit', function(e) {
        const xMin = parseInt(document.getElementById('tempo_destaque_minutos').value) || 0;
        const xSeg = parseInt(document.getElementById('tempo_destaque_segundos').value) || 0;
        const x = (xMin * 60) + xSeg;

        const yMin = parseInt(document.getElementById('tempo_alerta_minutos').value) || 0;
        const ySeg = parseInt(document.getElementById('tempo_alerta_segundos').value) || 0;
        const y = (yMin * 60) + ySeg;

        const zMin = parseInt(document.getElementById('tempo_kill_minutos').value) || 0;
        const zSeg = parseInt(document.getElementById('tempo_kill_segundos').value) || 0;
        const z = (zMin * 60) + zSeg;

        if (x >= y || y >= z) {
            e.preventDefault();
            alert('Atenção!\n\nOs tempos totais devem seguir a ordem: X < Y < Z\n\n' +
                  'Tempos configurados:\n' +
                  'X (Destaque): ' + xMin + 'min ' + xSeg + 'seg = ' + x + ' segundos\n' +
                  'Y (Alerta): ' + yMin + 'min ' + ySeg + 'seg = ' + y + ' segundos\n' +
                  'Z (Kill): ' + zMin + 'min ' + zSeg + 'seg = ' + z + ' segundos\n\n' +
                  'Por favor, ajuste os valores.');
            return false;
        }

        if (x == 0) {
            e.preventDefault();
            alert('Atenção!\n\nO tempo de destaque (X) deve ser maior que zero.');
            return false;
        }
    });
</script>
@endsection
