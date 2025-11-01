<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoTema extends Model
{
    use HasFactory;

    protected $table = 'configuracao_tema';

    protected $fillable = [
        'cor_primaria',
        'cor_secundaria',
    ];

    // Retorna a configuração de tema ou cria uma padrão
    public static function getConfiguracao()
    {
        $configuracao = self::first();

        if (!$configuracao) {
            $configuracao = self::create([
                'cor_primaria' => '#667eea',
                'cor_secundaria' => '#764ba2',
            ]);
        }

        return $configuracao;
    }
}
