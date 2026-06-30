<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atendente_id')->constrained('users')->onDelete('cascade');
            $table->string('cliente_nome');
            $table->string('cliente_email')->nullable();
            $table->string('cliente_telefone')->nullable();
            $table->date('data');
            $table->time('hora');
            $table->enum('status', ['agendado', 'cancelado', 'concluido'])->default('agendado');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};