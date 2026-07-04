<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterClienteRequest $request): JsonResponse
    {
        $dados = $request->validated();

        $cliente = Cliente::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'telefone' => $dados['telefone'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
        ]);

        $token = $cliente->createToken('api-token')->plainTextToken;

        Log::info('Novo cliente registrado', ['id_cliente' => $cliente->id_cliente]);

        return response()->json([
            'cliente' => $cliente,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dados = $request->validated();

        $cliente = Cliente::where('email', $dados['email'])->first();

        if (! $cliente || ! Hash::check($dados['password'], $cliente->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        $token = $cliente->createToken('api-token')->plainTextToken;

        Log::info('Cliente autenticado', ['id_cliente' => $cliente->id_cliente]);

        return response()->json([
            'cliente' => $cliente,
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}