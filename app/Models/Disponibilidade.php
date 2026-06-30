<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicial',
        'hora_final',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function atendente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}