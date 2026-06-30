<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@agendamento.com',
            'password' => Hash::make('admin123'),
            'perfil'   => 'administrador',
            'ativo'    => true,
        ]);

        User::create([
            'name'     => 'João Atendente',
            'email'    => 'joao@agendamento.com',
            'password' => Hash::make('admin123'),
            'perfil'   => 'atendente',
            'ativo'    => true,
        ]);

        User::create([
            'name'     => 'Maria Atendente',
            'email'    => 'maria@agendamento.com',
            'password' => Hash::make('admin123'),
            'perfil'   => 'atendente',
            'ativo'    => true,
        ]);
    }
}