<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'atendente_id',
        'cliente_nome',
        'cliente_email',
        'cliente_telefone',
        'data',
        'hora',
        'status',
        'observacao',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendente_id');
    }
}