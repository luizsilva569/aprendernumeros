<?php
namespace App\Http\Api_core;

class FuncoesGlobais
{
    /**
     * lida com os erros de validação de dados requeridos nas requisições
     * trata os erros e devolve um array para ser inserido no response
     */
    public static function converte_exceptionValidacao_em_array(\Illuminate\Validation\ValidationException $exception): array
    {
        //separa somente o campo dos erros.
        $erros = $exception->errors();

        //percorre a exception e adiciona junta os erros em um unico array pois a exception cria um array para cada erro.
        //dessa forma crio uma linha para cada erro em vez de um array pra cada.
        $convertidos = [];
        foreach ($erros as $campo => $mensagens) {
            foreach ($mensagens as $mensagem) {
                $convertidos[] = "$campo: $mensagem";
            }
        }
        // Substitui as palavras desejadas
        $convertidos = str_replace('validation.required', 'Este campo é obrigatório', $convertidos);
        $convertidos = str_replace('validation.min.string', 'Quantidade mínima de caracteres insuficiente', $convertidos);
        $convertidos = str_replace('validation.unique', 'Não disponível, já utilizado', $convertidos);
        $convertidos = str_replace('validation.email', 'Email não validado', $convertidos);


        return $convertidos;
    }
}
