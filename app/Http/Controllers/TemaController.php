<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracaoTema;

class TemaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_admin']);
    }

    public function edit()
    {
        $tema = ConfiguracaoTema::getConfiguracao();

        // Temas pré-definidos
        $temasPreDefinidos = [
            [
                'nome' => 'Roxo Gradiente (Padrão)',
                'cor_primaria' => '#667eea',
                'cor_secundaria' => '#764ba2',
            ],
            [
                'nome' => 'Azul Oceano',
                'cor_primaria' => '#2193b0',
                'cor_secundaria' => '#6dd5ed',
            ],
            [
                'nome' => 'Verde Natureza',
                'cor_primaria' => '#56ab2f',
                'cor_secundaria' => '#a8e063',
            ],
            [
                'nome' => 'Laranja Pôr do Sol',
                'cor_primaria' => '#f46b45',
                'cor_secundaria' => '#eea849',
            ],
            [
                'nome' => 'Rosa Romântico',
                'cor_primaria' => '#ec008c',
                'cor_secundaria' => '#fc6767',
            ],
            [
                'nome' => 'Vermelho Intenso',
                'cor_primaria' => '#cb2d3e',
                'cor_secundaria' => '#ef473a',
            ],
            [
                'nome' => 'Cinza Escuro',
                'cor_primaria' => '#232526',
                'cor_secundaria' => '#414345',
            ],
            [
                'nome' => 'Azul Índigo',
                'cor_primaria' => '#4776e6',
                'cor_secundaria' => '#8e54e9',
            ],
        ];

        return view('tema.edit', compact('tema', 'temasPreDefinidos'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cor_primaria' => 'required|string|max:7',
            'cor_secundaria' => 'required|string|max:7',
        ]);

        $tema = ConfiguracaoTema::getConfiguracao();

        $tema->update([
            'cor_primaria' => $request->cor_primaria,
            'cor_secundaria' => $request->cor_secundaria,
        ]);

        return redirect()->route('tema.edit')
            ->with('success', 'Tema atualizado com sucesso!');
    }
}
