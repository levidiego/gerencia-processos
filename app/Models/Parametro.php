<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $fillable = [
        'tempo_destaque_minutos',
        'tempo_destaque_segundos',
        'tempo_alerta_minutos',
        'tempo_alerta_segundos',
        'tempo_kill_minutos',
        'tempo_kill_segundos',
    ];

    protected $casts = [
        'tempo_destaque_minutos' => 'integer',
        'tempo_destaque_segundos' => 'integer',
        'tempo_alerta_minutos' => 'integer',
        'tempo_alerta_segundos' => 'integer',
        'tempo_kill_minutos' => 'integer',
        'tempo_kill_segundos' => 'integer',
    ];

    public static function getParametros()
    {
        $parametro = self::first();

        if (!$parametro) {
            $parametro = self::create([
                'tempo_destaque_minutos' => 5,
                'tempo_destaque_segundos' => 0,
                'tempo_alerta_minutos' => 10,
                'tempo_alerta_segundos' => 0,
                'tempo_kill_minutos' => 15,
                'tempo_kill_segundos' => 0,
            ]);
        }

        return $parametro;
    }

    // Retorna o tempo total de destaque em segundos
    public function getTempoDestaqueSegundosTotalAttribute()
    {
        return ($this->tempo_destaque_minutos * 60) + ($this->tempo_destaque_segundos ?? 0);
    }

    // Retorna o tempo total de alerta em segundos
    public function getTempoAlertaSegundosTotalAttribute()
    {
        return ($this->tempo_alerta_minutos * 60) + ($this->tempo_alerta_segundos ?? 0);
    }

    // Retorna o tempo total de kill em segundos
    public function getTempoKillSegundosTotalAttribute()
    {
        return ($this->tempo_kill_minutos * 60) + ($this->tempo_kill_segundos ?? 0);
    }
}
