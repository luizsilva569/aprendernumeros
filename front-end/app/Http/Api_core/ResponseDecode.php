<?php

namespace App\Http\Api_core;

use App\Http\Api_core\ResponseModel;

class ResponseDecode
{
    public function respostaModel($jsonData)
    {
        // Verificando se o JSON está vazio
        if (empty($jsonData)) {
            return new ResponseModel([
                'status' => 400,
                'mensagem' => 'Dados não recebidos da requisição',
                'errors' => ['dados' => "Nenhum dado foi recebido"],
            ]);
        }

         // Verifique se a variável é um array antes de decodificá-la
         $data = is_array($jsonData) ? $jsonData : json_decode($jsonData, true);

        // Verificando se houve erro na decodificação do JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new ResponseModel([
                'status' => 400,
                'mensagem' => 'Erro na decodificação do JSON',
                'errors' => ['json' => json_last_error_msg()],
            ]);
        }

         // Se o JSON contiver um corpo com dados, extraia as informações
         if (isset($data['body'])) {
            return new ResponseModel($data['body']);
        }

        // Retorna um modelo padrão caso não exista o campo 'body'
        return new ResponseModel($data);
    }
}
