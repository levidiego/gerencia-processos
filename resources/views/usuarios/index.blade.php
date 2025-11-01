@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="text-white"><i class="bi bi-people"></i> Usuários</h2>
            <p class="text-white-50">Gerencie os usuários do sistema</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Novo Usuário
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-list-ul"></i> Lista de Usuários
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Cadastrado em</th>
                                    <th class="text-center" style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->id }}</td>
                                        <td>{{ $usuario->name }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->is_admin)
                                                <span class="badge bg-danger">Administrador</span>
                                            @else
                                                <span class="badge bg-secondary">Usuário</span>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($usuario->id !== auth()->id())
                                                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" style="display: inline;"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Nenhum usuário encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
