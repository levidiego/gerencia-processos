<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Parametro;
use App\Models\ProcessoLog;

class ProcessosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filtrarBloqueados = $request->get('bloqueados', false);

        try {
            $query = "EXEC sp_whoisactive2";
            $processos = DB::select($query);

            $processos = collect($processos)->map(function($processo) {
                return (array) $processo;
            })->toArray();

            if ($filtrarBloqueados) {
                $processos = array_filter($processos, function($processo) {
                    return !empty($processo['blocking_session_id']);
                });
            }

            $parametros = Parametro::getParametros();
            $processos = $this->processarDestaques($processos, $parametros, $filtrarBloqueados);

            return view('processos.index', compact('processos', 'filtrarBloqueados', 'parametros'));

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao buscar processos: ' . $e->getMessage());
        }
    }

    private function processarDestaques($processos, $parametros, $filtrarBloqueados)
    {
        $sessionIds = array_column($processos, 'session_id');

        foreach ($processos as &$processo) {
            $processo['destacar_session'] = false;
            $processo['destacar_blocking'] = false;
            $processo['alertar'] = false;
            $processo['kill_automatico'] = false;

            $tempoSegundos = $this->converterTempoParaSegundos($processo['dd_hh_mm_ss_mss'] ?? '');

            // Verificar se este processo está bloqueando outros processos
            $ehBloqueador = in_array($processo['session_id'], array_column($processos, 'blocking_session_id'));

            // Verificar se o próprio processo NÃO está sendo bloqueado (campo blocking_session_id vazio)
            $naoEstaSendoBloqueado = empty($processo['blocking_session_id']);

            // Só destaca se for bloqueador, não estiver sendo bloqueado e tiver tempo >= X
            if ($ehBloqueador && $naoEstaSendoBloqueado && $tempoSegundos >= $parametros->tempo_destaque_segundos_total) {
                $processo['destacar_session'] = true;

                if ($tempoSegundos >= $parametros->tempo_alerta_segundos_total) {
                    $processo['alertar'] = true;
                }

                if ($tempoSegundos >= $parametros->tempo_kill_segundos_total) {
                    $processo['kill_automatico'] = true;
                }
            }

            if ($filtrarBloqueados && !empty($processo['blocking_session_id'])) {
                if (!in_array($processo['blocking_session_id'], $sessionIds)) {
                    $processo['destacar_blocking'] = true;
                }
            }
        }

        return $processos;
    }

    /**
     * Converte tempo no formato dd hh:mm:ss.mss para segundos
     * Formato do SQL Server: "dd hh:mm:ss.mss" (com espaço entre dias e horas)
     *
     * @param string $tempo
     * @return int
     */
    private function converterTempoParaSegundos($tempo)
    {
        if (empty($tempo)) return 0;

        // Formato esperado: dd hh:mm:ss.mss (com ESPAÇO entre dias e horas)
        // Exemplo: "00 00:00:42.157" = 42 segundos
        if (preg_match('/^(\d+)\s+(\d+):(\d+):(\d+)\.(\d+)$/', $tempo, $matches)) {
            $dias = (int)$matches[1];
            $horas = (int)$matches[2];
            $minutos = (int)$matches[3];
            $segundos = (int)$matches[4];
            // milissegundos ignorados para o cálculo

            return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
        }

        // Fallback: tentar formato alternativo dd:hh:mm:ss.mss (sem espaço)
        $partes = explode(':', $tempo);
        if (count($partes) >= 4) {
            $dias = (int)$partes[0];
            $horas = (int)$partes[1];
            $minutos = (int)$partes[2];

            $segundosPartes = explode('.', $partes[3]);
            $segundos = (int)$segundosPartes[0];

            return ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;
        }

        return 0;
    }

    public function kill(Request $request)
    {
        $request->validate(['session_id' => 'required|integer']);

        $sessionId = $request->session_id;

        try {
            // Buscar dados do processo antes de finalizar
            $processos = DB::select("EXEC sp_whoisactive2");
            $processoParaKill = null;

            foreach ($processos as $processo) {
                $processo = (array) $processo;
                if ($processo['session_id'] == $sessionId) {
                    $processoParaKill = $processo;
                    break;
                }
            }

            // Executar o KILL
            DB::statement("KILL $sessionId");

            // Gravar log do processo finalizado
            if ($processoParaKill) {
                ProcessoLog::create([
                    'session_id' => $sessionId,
                    'sql_text' => $processoParaKill['sql_text'] ?? null,
                    'dd_hh_mm_ss_mss' => $processoParaKill['dd_hh_mm_ss_mss'] ?? null,
                    'login_name' => $processoParaKill['login_name'] ?? null,
                    'status' => $processoParaKill['status'] ?? null,
                    'host_name' => $processoParaKill['host_name'] ?? null,
                    'database_name' => $processoParaKill['database_name'] ?? null,
                    'program_name' => $processoParaKill['program_name'] ?? null,
                    'tipo_kill' => 'manual',
                    'killed_by' => auth()->id(),
                    'killed_at' => now(),
                ]);
            }

            return back()->with('success', "Processo $sessionId finalizado com sucesso!");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao finalizar processo: ' . $e->getMessage());
        }
    }

    public function verificarAlertas()
    {
        try {
            $processos = DB::select("EXEC sp_whoisactive2");
            $parametros = Parametro::getParametros();
            $alertar = false;

            foreach ($processos as $processo) {
                $processo = (array) $processo;
                $tempoSegundos = $this->converterTempoParaSegundos($processo['dd_hh_mm_ss_mss'] ?? '');

                // Verificar se este processo está bloqueando outros processos
                $ehBloqueador = in_array($processo['session_id'], array_column($processos, 'blocking_session_id'));

                // Verificar se o próprio processo NÃO está sendo bloqueado
                $naoEstaSendoBloqueado = empty($processo['blocking_session_id']);

                if ($ehBloqueador && $naoEstaSendoBloqueado && $tempoSegundos >= $parametros->tempo_alerta_segundos_total) {
                    $alertar = true;
                    break;
                }
            }

            return response()->json(['alertar' => $alertar]);
        } catch (\Exception $e) {
            return response()->json(['alertar' => false]);
        }
    }
}
