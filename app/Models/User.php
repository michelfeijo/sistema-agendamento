<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'perfil',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ativo' => 'boolean',
    ];

    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidade::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'atendente_id');
    }

    public function isAdmin(): bool
    {
        return $this->perfil === 'administrador';
    }
}