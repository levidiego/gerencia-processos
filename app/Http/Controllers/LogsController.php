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

        // Filtro por tipo de kill
        if ($request->has('tipo_kill') && in_array($request->tipo_kill, ['manual', 'automatico'])) {
            $query->where('tipo_kill', $request->tipo_kill);
        }

        // Filtro por período
        if ($request->has('data_inicio') && $request->data_inicio) {
            $query->whereDate('killed_at', '>=', $request->data_inicio);
        }

        if ($request->has('data_fim') && $request->data_fim) {
            $query->whereDate('killed_at', '<=', $request->data_fim);
        }

        // Filtro por session_id
        if ($request->has('session_id') && $request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        // Paginação
        $logs = $query->paginate(20);

        return view('logs.index', compact('logs'));
    }
}
