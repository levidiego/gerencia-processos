<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;

class ParametrosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $parametros = Parametro::getParametros();
        return view('parametros.index', compact('parametros'));
    }

    public function edit()
    {
        $parametros = Parametro::getParametros();
        return view('parametros.edit', compact('parametros'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tempo_destaque_minutos' => 'required|integer|min:0',
            'tempo_destaque_segundos' => 'required|integer|min:0|max:59',
            'tempo_alerta_minutos' => 'required|integer|min:0',
            'tempo_alerta_segundos' => 'required|integer|min:0|max:59',
            'tempo_kill_minutos' => 'required|integer|min:0',
            'tempo_kill_segundos' => 'required|integer|min:0|max:59',
        ]);

        // Calcula os tempos totais em segundos para validação
        $tempoDestaqueTotal = ($request->tempo_destaque_minutos * 60) + $request->tempo_destaque_segundos;
        $tempoAlertaTotal = ($request->tempo_alerta_minutos * 60) + $request->tempo_alerta_segundos;
        $tempoKillTotal = ($request->tempo_kill_minutos * 60) + $request->tempo_kill_segundos;

        // Valida se X < Y < Z
        if ($tempoDestaqueTotal >= $tempoAlertaTotal || $tempoAlertaTotal >= $tempoKillTotal) {
            return back()
                ->withInput()
                ->with('error', 'Os tempos devem seguir a ordem: Destaque < Alerta < Kill');
        }

        // Valida se pelo menos um tempo foi configurado
        if ($tempoDestaqueTotal == 0) {
            return back()
                ->withInput()
                ->with('error', 'O tempo de destaque deve ser maior que zero');
        }

        $parametros = Parametro::getParametros();

        $parametros->update([
            'tempo_destaque_minutos' => $request->tempo_destaque_minutos,
            'tempo_destaque_segundos' => $request->tempo_destaque_segundos,
            'tempo_alerta_minutos' => $request->tempo_alerta_minutos,
            'tempo_alerta_segundos' => $request->tempo_alerta_segundos,
            'tempo_kill_minutos' => $request->tempo_kill_minutos,
            'tempo_kill_segundos' => $request->tempo_kill_segundos,
        ]);

        return redirect()->route('parametros.index')
            ->with('success', 'Parâmetros atualizados com sucesso!');
    }
}
