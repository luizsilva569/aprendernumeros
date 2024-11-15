<?php

namespace App\Http\Api_core;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class API
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    // Este método sempre será o chamado para fazer as requisições
    public function Post($url, $dados)
    {
        $url = config('api.url') . $url;
        return $this->Enviar($url, self::METHOD_POST, $dados);
    }

    // Método Get
    public function Get($url, $dados = null)
    {
        $url = config('api.url') . $url;
        return $this->Enviar($url, self::METHOD_GET, $dados);
    }

    // Centraliza em um único código o processo de envio de requisições.
    private function Enviar($url, $metodo, $dados = null, $token = null)
    {
        $token = $token ?: session('token');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->$metodo($url, $dados);

        // Log da resposta
        Log::info('Resposta da API: ', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        // Criar uma instância de ResponseDecode
        $responseDecode = new ResponseDecode();

        // Tratamento de erros
        $body = $response->json();
        if (!$response->successful()) {
            Log::error('Erro ao enviar requisição: ', [
                'url' => $url,
                'status' => $body['status'] ?? null,
                'body' => $body
            ]);

            // Retorna um ResponseModel com informações de erro
            return $responseDecode->respostaModel($body); // Passa o corpo da resposta
        }

        // Se a requisição for bem-sucedida, retorne os dados da resposta
        return $responseDecode->respostaModel($body); // Também retorna o corpo da resposta
    }
}
