<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'sql_text',
        'dd_hh_mm_ss_mss',
        'login_name',
        'status',
        'host_name',
        'database_name',
        'program_name',
        'tipo_kill',
        'killed_by',
        'killed_at',
    ];

    protected $dates = [
        'killed_at',
        'created_at',
        'updated_at',
    ];

    // Relacionamento com o usuário que executou o kill
    public function usuario()
    {
        return $this->belongsTo(User::class, 'killed_by');
    }
}
