@extends('layouts.app')

@section('title', 'Configurar Tema')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-white"><i class="bi bi-palette"></i> Configurar Tema</h2>
            <p class="text-white-50">Personalize as cores da interface do sistema</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-brush"></i> Cores do Tema
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tema.update') }}" id="formTema">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cor_primaria" class="form-label">
                                    <strong>Cor Primária</strong>
                                    <small class="text-muted d-block">Primeira cor do gradiente</small>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('cor_primaria') is-invalid @enderror"
                                           id="cor_primaria" name="cor_primaria"
                                           value="{{ old('cor_primaria', $tema->cor_primaria) }}" required>
                                    <input type="text" class="form-control" id="cor_primaria_text"
                                           value="{{ old('cor_primaria', $tema->cor_primaria) }}"
                                           readonly>
                                </div>
                                @error('cor_primaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cor_secundaria" class="form-label">
                                    <strong>Cor Secundária</strong>
                                    <small class="text-muted d-block">Segunda cor do gradiente</small>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('cor_secundaria') is-invalid @enderror"
                                           id="cor_secundaria" name="cor_secundaria"
                                           value="{{ old('cor_secundaria', $tema->cor_secundaria) }}" required>
                                    <input type="text" class="form-control" id="cor_secundaria_text"
                                           value="{{ old('cor_secundaria', $tema->cor_secundaria) }}"
                                           readonly>
                                </div>
                                @error('cor_secundaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Pré-visualização do Gradiente</strong></label>
                            <div id="preview" style="height: 100px; border-radius: 10px; background: linear-gradient(135deg, {{ $tema->cor_primaria }} 0%, {{ $tema->cor_secundaria }} 100%);"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Salvar Tema
                            </button>
                            <a href="{{ route('processos.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-stars"></i> Temas Pré-Definidos
                </div>
                <div class="card-body">
                    <p class="small text-muted">Clique em um tema para aplicá-lo:</p>

                    @foreach($temasPreDefinidos as $temaPreDef)
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 aplicar-tema"
                                data-primaria="{{ $temaPreDef['cor_primaria'] }}"
                                data-secundaria="{{ $temaPreDef['cor_secundaria'] }}"
                                style="background: linear-gradient(135deg, {{ $temaPreDef['cor_primaria'] }} 0%, {{ $temaPreDef['cor_secundaria'] }} 100%); color: white; border: none;">
                            {{ $temaPreDef['nome'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Atualizar preview ao mudar as cores
    document.getElementById('cor_primaria').addEventListener('input', function() {
        document.getElementById('cor_primaria_text').value = this.value;
        atualizarPreview();
    });

    document.getElementById('cor_secundaria').addEventListener('input', function() {
        document.getElementById('cor_secundaria_text').value = this.value;
        atualizarPreview();
    });

    function atualizarPreview() {
        const corPrimaria = document.getElementById('cor_primaria').value;
        const corSecundaria = document.getElementById('cor_secundaria').value;
        document.getElementById('preview').style.background = 'linear-gradient(135deg, ' + corPrimaria + ' 0%, ' + corSecundaria + ' 100%)';
    }

    // Aplicar tema pré-definido
    document.querySelectorAll('.aplicar-tema').forEach(function(button) {
        button.addEventListener('click', function() {
            const corPrimaria = this.getAttribute('data-primaria');
            const corSecundaria = this.getAttribute('data-secundaria');

            document.getElementById('cor_primaria').value = corPrimaria;
            document.getElementById('cor_primaria_text').value = corPrimaria;
            document.getElementById('cor_secundaria').value = corSecundaria;
            document.getElementById('cor_secundaria_text').value = corSecundaria;

            atualizarPreview();
        });
    });
</script>
@endsection
