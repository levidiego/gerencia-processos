@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-white"><i class="bi bi-pencil"></i> Editar Usuário</h2>
            <p class="text-white-50">Altere os dados do usuário</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge"></i> Dados do Usuário
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $usuario->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_admin') is-invalid @enderror"
                                       id="is_admin" name="is_admin" value="1" {{ old('is_admin', $usuario->is_admin) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">
                                    <strong>Administrador</strong>
                                    <small class="text-muted d-block">
                                        Administradores têm acesso a Parâmetros, Logs e Gerenciamento de Usuários
                                    </small>
                                </label>
                                @error('is_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Para alterar a senha, use a opção "Trocar Senha" no menu do usuário.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Atualizar
                            </button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
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
