<?php

namespace App\Http\Api_core;

// Classe que cria uma model da resposta para o usuário
class ResponseModel
{
    public $status;
    public $tempo_gasto;
    public $mensagem;
    public $errors;
    public $api_version;
    public $time_response;
    public $date_time;
    public $date_default_timezone;
    public $sucesso;
    public $objeto;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? null;
        $this->tempo_gasto = $data['tempo_gasto'] ?? null;
        $this->mensagem = $data['mensagem'] ?? null;
        $this->errors = $data['errors'] ?? null;
        $this->api_version = $data['api_version'] ?? null;
        $this->time_response = $data['time_response'] ?? null;
        $this->date_time = $data['date_time'] ?? null;
        $this->date_default_timezone = $data['date_default_timezone'] ?? null;
        $this->sucesso = $data['sucesso'] ?? null;

        // Aceitar qualquer tipo de dado para o objeto, mantendo-o como array
        if (!empty($data['objeto'])) {
            // Verifica se o 'objeto' é um array e não cria uma nova instância se não for necessário
            $this->objeto = is_array($data['objeto']) ? $data['objeto'] : $data['objeto'];
        } else {
            $this->objeto = null; // ou você pode usar: $this->objeto = [];
        }
    }
}
