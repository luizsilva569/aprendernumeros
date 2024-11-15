<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Api_core\API;
use Illuminate\Support\Facades\Log;

class QuestoesController extends Controller
{

    private function obterNovaQuestao()
    {
        $api = new API();
        $url = "/api/numeros/novo";
        $response = $api->Get($url);

        // Log da resposta da API
        Log::info('Resposta da API: ', (array) $response);

        // Verifica se houve erro na resposta
        if ($response->status > 299) {
            Log::error('Erro ao buscar nova questão: ' . $response->mensagem);
        }

        //verifica e salva o token na sessao
        if ($response->status <= 299 && isset($response->objeto['token_questao'])) {
            session(['token_questao' => $response->objeto['token_questao']]);
        }
        return $response;
    }

    public function novaQuestao()
    {
        $response = $this->obterNovaQuestao();
        //retorna a resposta como responseModel
        return view('questoes', ['response' => $response]);
    }

    public function novaQuestaoBotao()
    {
        $response = $this->obterNovaQuestao();
        // Retorna a resposta como JSON  ;

        return response()->json($response);
    }

    /*
    "objeto":{
        "acertou":true,
        "resposta_usuario":"18",
        "resposta_correta":"18",
        "pontuacao":10,
        "pontuacao_total":10

    */

    public function responderQuestao(Request $request)
{
    $api = new API();
    $url = "/api/numeros/novo";

    $token_questao = session('token_questao');
    $dados = [
        'token_questao' => $token_questao,
        'resposta' => $request->input('resposta')
    ];

    Log::info('url: ' . $url . " - token_questao: ". $token_questao . " resposta: " . $request->input('resposta'));

    // Faz a requisição
    $response = $api->Post($url, $dados);

    if ($response->status !== 200) {
        Log::info('Erro:::Resposta da API: ', (array) $response);
        session()->flash('status', $response->objeto);
    } else {
        if ($response->objeto) {
            session()->flash('status', $response->objeto);
        } else {
            session()->flash('status', 'Erro na requisição.');
        }
    }
    return response()->json($response);
    // Redireciona para a página de nova questão
  //  return redirect()->route('questoes.nova');
}


}
