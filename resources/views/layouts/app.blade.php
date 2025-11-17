@php
    $tema = \App\Models\ConfiguracaoTema::getConfiguracao();
    $corPrimaria = $tema->cor_primaria ?? '#667eea';
    $corSecundaria = $tema->cor_secundaria ?? '#764ba2';
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gerência de Processos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            background-attachment: fixed;
        }
        .navbar {
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%) !important;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        .navbar-nav .nav-link {
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            transform: translateY(-2px);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,.15);
        }
        .card-header {
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.25rem;
            font-weight: 600;
        }
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            border: none;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,.1);
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.625rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Destaques específicos do gerenciador de processos */
        .destaque-session {
            background-color: #ff9800 !important;
            color: white !important;
            font-weight: bold;
        }
        .destaque-blocking {
            background-color: #ffeb3b !important;
            color: #000 !important;
        }
        .alerta-kill {
            background-color: #f44336 !important;
            color: white !important;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Ajustes da paginação */
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 0.25rem;
            border: none;
            background-color: white;
            color: #667eea;
            transition: all 0.3s ease;
        }
        .pagination .page-link:hover {
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            color: white;
            transform: translateY(-2px);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, {{ $corPrimaria }} 0%, {{ $corSecundaria }} 100%);
            color: white;
        }
        /* Reduzir tamanho dos ícones < e > da paginação */
        .pagination .page-link i,
        .pagination .page-link i.bi,
        .pagination .page-link i.bi-chevron-left,
        .pagination .page-link i.bi-chevron-right {
            font-size: 0.75rem !important;
            width: auto !important;
            height: auto !important;
        }

        /* Força o tamanho em todos os contextos */
        nav .pagination .page-link i {
            font-size: 0.75rem !important;
        }

        .card-footer .pagination .page-link i {
            font-size: 0.75rem !important;
        }

        /* Responsividade mobile */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
            }
            .table {
                font-size: 0.75rem;
            }
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            .navbar-brand {
                font-size: 1rem;
            }
            .table td, .table th {
                padding: 0.5rem 0.25rem;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container > *, .container-fluid > * {
            animation: fadeIn 0.5s ease;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('processos.index') }}">
                <i class="bi bi-hdd-rack"></i> Gerência de Processos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('processos.index') }}">
                                <i class="bi bi-cpu"></i> Processos
                            </a>
                        </li>
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logs.index') }}">
                                    <i class="bi bi-journal-text"></i> Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('usuarios.index') }}">
                                    <i class="bi bi-people"></i> Usuários
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('parametros.index') }}">
                                    <i class="bi bi-gear"></i> Parâmetros
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tema.edit') }}">
                                    <i class="bi bi-palette"></i> Tema
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-house-door"></i> Início</a></li>
                                <li><a class="dropdown-item" href="{{ route('senha.edit') }}"><i class="bi bi-key"></i> Trocar Senha</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Sair
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-3 px-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
