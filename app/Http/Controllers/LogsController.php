<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcessoLog;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = ProcessoLog::with('usuario')->orderBy('killed_at', 'desc');

        // Define data de hoje como padrão APENAS se for a primeira visita (sem parâmetros)
        $dataHoje = now()->format('Y-m-d');
        $primeiraVisita = !$request->hasAny(['data_inicio', 'data_fim', 'tipo_kill', 'session_id']);

        $dataInicio = $primeiraVisita ? $dataHoje : $request->input('data_inicio');
        $dataFim = $primeiraVisita ? $dataHoje : $request->input('data_fim');

        // Filtro por tipo de kill
        if ($request->has('tipo_kill') && in_array($request->tipo_kill, ['manual', 'automatico'])) {
            $query->where('tipo_kill', $request->tipo_kill);
        }

        // Filtro por período (apenas se houver valores)
        if ($dataInicio) {
            $query->whereDate('killed_at', '>=', $dataInicio);
        }

        if ($dataFim) {
            $query->whereDate('killed_at', '<=', $dataFim);
        }

        // Filtro por session_id
        if ($request->has('session_id') && $request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        // Paginação com preservação de query string
        $logs = $query->paginate(20)->appends($request->except('page'));

        return view('logs.index', compact('logs', 'dataInicio', 'dataFim'));
    }
}
