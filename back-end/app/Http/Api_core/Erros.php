<?php

namespace App\Http\Api_core;

class Erros
{
    // Define os erros como constantes com códigos e mensagens associadas
    const ERRO_API_MANUTENCAO = [
        'sucesso' => false,
        'status' => 500,
        'codigo' => '001',
        'mensagem' => 'API em manutenção.'
    ];

    const ERRO_CONEXAO_BD = [
        'sucesso' => false,
        'status' => 500,
        'codigo' => '002',
        'mensagem' => 'Falha de comunicação com o banco de dados.'
    ];

    const ERRO_VALIDACAO = [
        'sucesso' => false,
        'status' => 422,
        'codigo' => '003',
        'mensagem' => 'Erro na validação dos dados.'
    ];

    const ERRO_PREPARAR_QUERY = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '004',
        'mensagem' => 'Erro interno.'
    ];

    const ERRO_INSERIR_DADOS = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '005',
        'mensagem' => 'Erro ao inserir os dados.'
    ];

    const ERRO_ALTERAR_DADOS = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '006',
        'mensagem' => 'Erro ao alterar os dados.'
    ];

    const ERRO_EXCLUIR_DADOS = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '007',
        'mensagem' => 'Erro ao excluir os dados.'
    ];

    const ERRO_APRESENTAR_DADOS = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '008',
        'mensagem' => 'Erro ao apresentar os dados.'
    ];

    const ERRO_PREPARAR_CONSULTA = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '009',
        'mensagem' => 'Erro interno.'
    ];

    const API_OFFLINE = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '010',
        'mensagem' => 'API em manutenção.'
    ];

    const DADOS_NAO_ENCONTRADOS = [
        'sucesso' => false,
        'status' => 400,
        'codigo' => '011',
        'mensagem' => 'Dados não encontrados.'
    ];

    const ERRO_INTERNO = [
        'sucesso' => false,
        'status' => 500,
        'codigo' => '012',
        'mensagem' => 'Ocorreu um erro interno.'
    ];

    const TOKEN_INVALIDO = [
        'sucesso' => false,
        'status' => 401,
        'codigo' => '013',
        'mensagem' => 'Autenticação inválida ou expirada.'
    ];

    const TOKEN_NAO_RECEBIDO = [
        'sucesso' => false,
        'status' => 401,
        'codigo' => '014',
        'mensagem' => 'Autenticação não recebida.'
    ];

    const  LOGIN_OU_SENHA_NAO_RECEBIDOS= [
        'sucesso' => false,
        'status' => 401,
        'codigo' => '015',
        'mensagem' => 'Login ou senha não recebidos.'
    ];

    const  LOGIN_OU_SENHA_INVALIDOS= [
        'sucesso' => false,
        'status' => 401,
        'codigo' => '016',
        'mensagem' => 'Dados inválidos'
    ];
}
