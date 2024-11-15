<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuario');
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'senha' => 'required|string|min:6|confirmed', // A senha deve ser confirmada
        ]);

        $dados = $request->only('nome', 'email', 'senha');
        $url = config('api.url') . '/api/usuarios/salvar';

        // Adiciona cabeçalho 'Accept' para aceitar JSON
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($url, $dados);

        Log::info('URL da API:', ['url' => $url]);
        Log::info('Dados enviados para a API:', $dados);
        Log::info('Resposta da API:', ['status' => $response->status(), 'body' => $response->json()]);

        if ($response->successful()) {
            $responseData = $response->json();
            $mensagem = $responseData['mensagem'] ?? 'Usuário cadastrado com sucesso!';

            return redirect()->route('questoes')
                ->with('mensagem_sucesso', $mensagem);
        } else {
            $errorMessage = $response->json()['mensagem'] ?? 'Erro desconhecido';
            $errors = $response->json()['errors'] ?? [];

            return back()->withErrors(['problema' => $errorMessage])
                ->withInput()
                ->with('response_com_loop_erros', ['mensagem' => $errorMessage, 'errors' => $errors]);
        }
    }
}
