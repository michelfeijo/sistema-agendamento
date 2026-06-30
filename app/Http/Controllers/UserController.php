<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'perfil', 'ativo')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'perfil'   => 'required|in:administrador,atendente',
        ], [
            'name.required'     => 'O nome é obrigatório.',
            'email.required'    => 'O e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'email.unique'      => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min'      => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed'=> 'A confirmação de senha não confere.',
            'perfil.required'   => 'O perfil é obrigatório.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'perfil'   => $request->perfil,
            'ativo'    => true,
        ]);

        return response()->json($user, 201);
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = $request->user();

        if (!$authUser->isAdmin() && $authUser->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'name'   => 'required|string|max:255',
            'perfil' => 'required|in:administrador,atendente',
        ], [
            'name.required'  => 'O nome é obrigatório.',
            'perfil.required'=> 'O perfil é obrigatório.',
        ]);

        $dadosAtualizar = ['name' => $request->name];

        // Apenas admin pode alterar o perfil
        if ($authUser->isAdmin()) {
            $dadosAtualizar['perfil'] = $request->perfil;
        }

        $user->update($dadosAtualizar);

        return response()->json($user);
    }

    public function toggleAtivo(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $user = User::findOrFail($id);
        $user->update(['ativo' => !$user->ativo]);

        $status = $user->ativo ? 'ativado' : 'inativado';

        return response()->json([
            'message' => "Usuário {$status} com sucesso.",
            'user'    => $user
        ]);
    }
}