<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController
{
    public function index()
    {
        return view('login');
    }

    public function efetuaLogin(Request $request)
    {
        // Valida os dados de entrada
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        $dados = $request->only('email', 'senha');
        $url = config('api.url') . '/api/login';

        // Adiciona cabeçalho 'Accept' para aceitar JSON
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($url, $dados);

        Log::info('URL da API:', ['url' => $url]);
        Log::info('Dados enviados para a API:', $dados);
        Log::info('Resposta da API:', ['status' => $response->status(), 'body' => $response->json()]);

        if ($response->successful()) {
            // Obter a resposta como array
            $responseData = $response->json(); // Aqui, usamos o método json() diretamente
            $token = $responseData['objeto']['token'];
            // Verifica se a chave 'objeto' e 'token' existem
            if ($token) {
                session([
                    'token' => $token,
                    'nome_usuario' => $responseData['objeto']['nome_usuario']
                ]);
                Log::info('Token:' . $token);

                // Redirecionar para a dashboard ou outra página
                return redirect()->route('questoes')->with('success', 'Login realizado com sucesso!');
            } else {
                // Se 'token' não estiver disponível
                return back()->withErrors(['login_error' => 'Token não encontrado na resposta da API.']);
            }
        } else {
            $errorMessage = $response->json()['mensagem'] ?? 'Erro desconhecido';
            $errors = $response->json()['errors'] ?? [];

            return back()->withErrors(['problema' => $errorMessage])
                ->withInput()
                ->with('response_com_loop_erros', ['mensagem' => $errorMessage, 'errors' => $errors]);
        }
    }

    public function logout()
    {
        // Encerrar a sessão do usuário
        Auth::logout();

        // Invalidar a sessão e remover todos os dados
        session()->invalidate();

        // Regenerar o token CSRF
        session()->regenerateToken();

        // Redirecionar para a página de login ou outra página
        return redirect('/login')->with('mensagem_sucesso', 'Você foi desconectado com sucesso!');
    }
}
