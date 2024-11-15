<?php

namespace App\Http\Controllers;

use App\Http\Api_core\API;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
    public function index()
    {

        $api = new API();
        $url = '/api/ranking';
        $response = $api->Get($url);



        if (is_null($response)) {
            Log::error('Resposta da API Nula:', ['body' => $response]);
            // Trate o caso em que a resposta é nula
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'A resposta da API é nula.',
            ], 500); // Use um código de status apropriado
        }
        if ($response) {
            Log::error('Resposta da API Ranking:', ['body' => $response]);
            return response()->json($response);
        } else {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao obter dados do ranking: ' . ($response->mensagem ?? 'Resposta inesperada.'),
            ], 400);
        }
    }
}
