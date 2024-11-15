<?php

namespace App\Http\Api_core;

class Sucesso
{
    // Define as mensagens como constantes com códigos e mensagens associadas
    const INSERIDO_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1000',
        'mensagem' => 'Dados inseridos com sucesso'
    ];

    const EXECUTADO_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1000',
        'mensagem' => 'Solicitação executada com sucesso'
    ];
    const ATUALIZADO_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1002',
        'mensagem' => 'Dados atualizados com sucesso'
    ];

    const EXCLUIDO_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1003',
        'mensagem' => "Dados excluídos com sucesso"
    ];

    const APRESENTADO_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1004',
        'mensagem' => "Dados apresentados com sucesso"
    ];

    const LOGIN_COM_SUCESSO = [
        'sucesso' => true,
        'status' => 200,
        'codigo' => '1005',
        'mensagem' => "Login realizado com sucesso"
    ];


}
