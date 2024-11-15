<?php

namespace App\Http\Api_core;

class TraducaoErros
{
    private static function getErros()
    {
        //Deve se cadastrar aqui os erros retornados pelo sistema... e suas traduções
        $errosCadastrados = [
            "a foreign key constraint fails" => "Erro 1001: Algum dado vinculado a este cadastro não foi encontrado.",
            "Duplicate entry" => "Erro 1002: Já Cadastrado",
           // "Integrity constraint violation" => "Erro 1003: Algum dado vinculado a este cadastro não foi encontrado.",
            //"Unknown column" => 'Erro 1004: Alguma coluna enviada não está cadastrada no Banco de dados '
        ];
        return $errosCadastrados;
    }

    public static function traduzirErro($erro)
    {

        $errosCadastrados = self::getErros();
        // Percorre o array $errosCadastrados para procurar correspondências
        foreach ($errosCadastrados as $erroCadastrado => $traducao) {
            //verifica se o erro contem parte dos erros cadastrados.
            if (strpos($erro, $erroCadastrado) !== false) {
                return $traducao;
            }
        }
        // Se não encontrar correspondência, retorna o erro original
        return $erro;
    }
}
