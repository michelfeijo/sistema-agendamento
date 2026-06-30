<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Disponibilidade;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $agendamentos = Agendamento::with('atendente:id,name')->get();
        } else {
            $agendamentos = Agendamento::with('atendente:id,name')
                ->where('atendente_id', $user->id)
                ->get();
        }

        return response()->json($agendamentos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'atendente_id'     => 'required|exists:users,id',
            'cliente_nome'     => 'required|string|max:255',
            'cliente_email'    => 'nullable|email',
            'cliente_telefone' => 'nullable|string|max:20',
            'data'             => 'required|date|after_or_equal:today',
            'hora'             => 'required|date_format:H:i',
            'observacao'       => 'nullable|string',
        ], [
            'atendente_id.required' => 'O atendente é obrigatório.',
            'atendente_id.exists'   => 'Atendente inválido.',
            'cliente_nome.required' => 'O nome do cliente é obrigatório.',
            'cliente_email.email'   => 'Informe um e-mail válido.',
            'data.required'         => 'A data é obrigatória.',
            'data.date'              => 'Informe uma data válida.',
            'data.after_or_equal'   => 'A data não pode ser no passado.',
            'hora.required'         => 'A hora é obrigatória.',
            'hora.date_format'      => 'Formato de hora inválido.',
        ]);

        $jaAgendado = Agendamento::where('atendente_id', $request->atendente_id)
            ->where('data', $request->data)
            ->where('hora', $request->hora)
            ->where('status', 'agendado')
            ->exists();

        if ($jaAgendado) {
            return response()->json([
                'message' => 'Este horário já está ocupado.'
            ], 400);
        }

        $agendamento = Agendamento::create($request->all());

        return response()->json($agendamento->load('atendente:id,name'), 201);
    }

    public function show($id)
    {
        $agendamento = Agendamento::with('atendente:id,name')->findOrFail($id);
        return response()->json($agendamento);
    }

    public function cancelar(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && $agendamento->atendente_id !== $user->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $agendamento->update(['status' => 'cancelado']);

        return response()->json(['message' => 'Agendamento cancelado com sucesso.']);
    }
}