<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Parametro;
use App\Models\ProcessoLog;

class KillProcessosAutomatico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processos:kill-automatico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finaliza automaticamente processos bloqueadores que ultrapassaram o tempo Z';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando verificação de processos para kill automático...');

        try {
            // Buscar processos ativos
            $processos = DB::select("EXEC sp_whoisactive2");
            $parametros = Parametro::getParametros();

            $processosFinalizados = 0;

            foreach ($processos as $processo) {
                $processo = (array) $processo;

                // Verificar se este processo está bloqueando outros processos
                $ehBloqueador = in_array($processo['session_id'], array_column($processos, 'blocking_session_id'));

                // Verificar se o próprio processo NÃO está sendo bloqueado
                $naoEstaSendoBloqueado = empty($processo['blocking_session_id']);

                if (!$ehBloqueador || !$naoEstaSendoBloqueado) {
                    continue; // Não é bloqueador OU está sendo bloqueado, pular
                }

                // Calcular tempo em segundos
                $tempoSegundos = $this->converterTempoParaSegundos($processo['dd_hh_mm_ss_mss'] ?? '');

                // Verificar se ultrapassou o tempo Z
                if ($tempoSegundos >= $parametros->tempo_kill_segundos_total) {
                    $sessionId = $processo['session_id'];

                    $this->warn("Finalizando processo bloqueador {$sessionId} (Tempo: {$processo['dd_hh_mm_ss_mss']})");

                    try {
                        // Executar KILL
                        DB::statement("KILL {$sessionId}");

                        // Registrar no log
                        ProcessoLog::create([
                            'session_id' => $sessionId,
                            'sql_text' => $processo['sql_text'] ?? null,
                            'dd_hh_mm_ss_mss' => $processo['dd_hh_mm_ss_mss'] ?? null,
                            'login_name' => $processo['login_name'] ?? null,
                            'status' => $processo['status'] ?? null,
                            'host_name' => $processo['host_name'] ?? null,
                            'database_name' => $processo['database_name'] ?? null,
                            'program_name' => $processo['program_name'] ?? null,
                            'tipo_kill' => 'automatico',
                            'killed_by' => null, // Kill automático não tem usuário
                            'killed_at' => now(),
                        ]);

                        $processosFinalizados++;

                        $this->info("✓ Processo {$sessionId} finalizado com sucesso");

                    } catch (\Exception $e) {
                        $this->error("✗ Erro ao finalizar processo {$sessionId}: {$e->getMessage()}");
                    }
                }
            }

            if ($processosFinalizados > 0) {
                $this->info("\nTotal de processos finalizados: {$processosFinalizados}");
            } else {
                $this->info('Nenhum processo precisou ser finalizado.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro ao verificar processos: ' . $e->getMessage());
            return 1;
        }
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
}
