<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disponibilidade;
use App\Models\User;

class DisponibilidadeController extends Controller
{
    public function index()
    {
        $disponibilidades = Disponibilidade::with('atendente:id,name')
            ->get();
        return response()->json($disponibilidades);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'dia_semana'   => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'hora_inicial' => 'required|date_format:H:i',
            'hora_final'   => 'required|date_format:H:i|after:hora_inicial',
            'ativo'        => 'boolean',
        ], [
            'user_id.required'      => 'O atendente é obrigatório.',
            'user_id.exists'        => 'Atendente inválido.',
            'dia_semana.required'   => 'O dia da semana é obrigatório.',
            'hora_inicial.required' => 'A hora inicial é obrigatória.',
            'hora_inicial.date_format' => 'Formato de hora inicial inválido.',
            'hora_final.required'   => 'A hora final é obrigatória.',
            'hora_final.date_format' => 'Formato de hora final inválido.',
            'hora_final.after'      => 'A hora final deve ser maior que a hora inicial.',
        ]);

        $disponibilidade = Disponibilidade::create($request->all());

        return response()->json($disponibilidade->load('atendente:id,name'), 201);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $disponibilidade = Disponibilidade::findOrFail($id);

        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'dia_semana'   => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'hora_inicial' => 'required|date_format:H:i',
            'hora_final'   => 'required|date_format:H:i|after:hora_inicial',
            'ativo'        => 'boolean',
        ], [
            'user_id.required'      => 'O atendente é obrigatório.',
            'user_id.exists'        => 'Atendente inválido.',
            'dia_semana.required'   => 'O dia da semana é obrigatório.',
            'hora_inicial.required' => 'A hora inicial é obrigatória.',
            'hora_inicial.date_format' => 'Formato de hora inicial inválido.',
            'hora_final.required'   => 'A hora final é obrigatória.',
            'hora_final.date_format' => 'Formato de hora final inválido.',
            'hora_final.after'      => 'A hora final deve ser maior que a hora inicial.',
        ]);

        $disponibilidade->update($request->all());

        return response()->json($disponibilidade->load('atendente:id,name'));
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $disponibilidade = Disponibilidade::findOrFail($id);
        $disponibilidade->delete();

        return response()->json(['message' => 'Disponibilidade removida com sucesso.']);
    }

    public function horariosDisponiveis(Request $request)
    {
        $request->validate([
            'atendente_id' => 'required|exists:users,id',
            'data'         => 'required|date',
        ]);

        $data = $request->data;
        $atendenteId = $request->atendente_id;

        $diaSemana = $this->getDiaSemana(date('w', strtotime($data)));

        $disponibilidades = Disponibilidade::where('user_id', $atendenteId)
            ->where('dia_semana', $diaSemana)
            ->where('ativo', true)
            ->get();

        if ($disponibilidades->isEmpty()) {
            return response()->json([]);
        }

        $agendados = \App\Models\Agendamento::where('atendente_id', $atendenteId)
            ->where('data', $data)
            ->where('status', 'agendado')
            ->pluck('hora')
            ->map(fn($h) => substr($h, 0, 5))
            ->toArray();

        $horariosDisponiveis = [];

        foreach ($disponibilidades as $disp) {
            $horaAtual = $disp->hora_inicial;
            $horaFinal = $disp->hora_final;

            while ($horaAtual < $horaFinal) {
                if (!in_array($horaAtual, $agendados)) {
                    $horariosDisponiveis[] = $horaAtual;
                }
                $horaAtual = date('H:i', strtotime($horaAtual . ' +30 minutes'));
            }
        }

        return response()->json($horariosDisponiveis);
    }

    private function getDiaSemana($numeroDia): string
    {
        $dias = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado',
        ];
        return $dias[$numeroDia];
    }
}