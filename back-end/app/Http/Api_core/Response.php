<?php

namespace App\Http\Api_core;

class Response
{

    //Esta resposta é utilizada para enviar uma mensagem pré-definida
    //as mensagens pré-definidas estão no arquivo erros e no arquivo sucesso...
    public static function jsonEnum(array $enum, $errors = null, $objeto = null, $tempo_gasto = null)
    {
        date_default_timezone_set('America/Sao_Paulo');
        header('Content-Type: application/json');


        $qdeErros = 0;

        // Verifica se errors é um array válido e conta seus elementos
        if (isset($errors) && is_array($errors)) {
            $qdeErros = count($errors);
        }

        $msgErro = "";
        if ($qdeErros > 1) {
            $msgErro = " Encontrados " . $qdeErros . " erros.";
        } elseif ($qdeErros > 0) {
            $msgErro = " Encontrado 1 erro.";
        }

        // Cria o JSON para ser retornado
        $json = [
            'status' => $enum['status'],
            'tempo_gasto' => $tempo_gasto,
            'mensagem' => $enum['mensagem'] . $msgErro,
            'errors' => $qdeErros > 0 ? $errors : null ,
            'api_version' => config('api.version'),
            'time_response' => time(),
            'date_time' => date("d-m-Y H:i:s"),
            'date_default_timezone' => date_default_timezone_get(),
            'sucesso' => $enum['sucesso'],
            'objeto' => $objeto
        ];


        return response()->json($json, $enum['status']);
    }


    //Esta resposta é utilziada para enviar mensagens personalzadas sem ser as padronizadas...
    public static function json($status = 200, $sucesso = true, $mensagem = '', $errors = null, $objeto = null)
    {
        header('Content-Type: application/json');

        $json = [
            'status' => $status,
            'mensagem' => $mensagem,
            'errors' => $errors,
            'api_version' => config('api.version'),
            'time_response' => time(),
            'date_time' => date("d/m/Y H:i:s"),
            'date_default_timezone' =>  date_default_timezone_get(),
            'sucesso' => $sucesso,
            'objeto' => $objeto,
        ];
        return  response()->json($json, $status);
    }
}
