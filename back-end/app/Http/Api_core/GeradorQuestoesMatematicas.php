<?php

namespace App\Http\Api_core;

use App\Http\Services\MetodosQuestoesService;
use App\Models\MetodosQuestoes;
use App\Models\Questao;
use Exception;

class GeradorQuestoesMatematicas
{
    // Enum de categorias
    const CATEGORIA_ALFABETIZACAO = "alfabetizacao";
    const CATEGORIA_INICIANTE = 'iniciante';
    const CATEGORIA_INTERMEDIARIO = 'intermediario';
    const CATEGORIA_AVANCADO = 'avancado';
    const CATEGORIA_SUPER = "super";

    // Enum de operadores$operadores matemáticos
    const OPERADOR_SOMA = 'Adição';
    const OPERADOR_SUBTRACAO = '-';
    const OPERADOR_MULTIPLICACAO = '*';
    const OPERADOR_DIVISAO = '/';

    /**
     * Gera uma lista de questões matemáticas baseadas nos parâmetros fornecidos.
     *
     * @param int $qdeDigitos_item1 Número de dígitos do primeiro número
     * @param int $qdeDigitos_item2 Número de dígitos do segundo número
     * @param string $operador Tipo de operação matemática
     * @param string $categoria Dificuldade da questão
     * @return array Lista de questões geradas
     */
    public static function gerarQuestoes($min_item1, $max_item1, $min_item2, $max_item2, $operador, $categoria)
    {
        $operadores = MetodosQuestoes::all();

        $questoes = [];
        $operador_id = 0;

        for ($n1 = $min_item1; $n1 <= $max_item1; $n1++) {
            for ($n2 = $min_item2; $n2 <= $max_item2; $n2++) {
                switch ($operador) {
                    case self::OPERADOR_SOMA:
                        //pega o id do operador
                        $operador_id = self::retornaID_operador($operadores, self::OPERADOR_SOMA);
                        $resultado = $n1 + $n2;
                        break;
                    case self::OPERADOR_SUBTRACAO:
                        $resultado = $n1 - $n2;
                        $operador_id = self::retornaID_operador($operadores, self::OPERADOR_SUBTRACAO);
                        break;
                    case self::OPERADOR_MULTIPLICACAO:
                        $resultado = $n1 * $n2;
                        $operador_id = self::retornaID_operador($operadores, self::OPERADOR_MULTIPLICACAO);
                        break;
                    case self::OPERADOR_DIVISAO:
                        if (
                            $n2
                            == 0
                        ) continue 2; // Evita divisão por zero
                        $resultado = $n1 / $n2;
                        $operador_id = self::retornaID_operador($operadores, self::OPERADOR_DIVISAO);
                        break;
                    default:
                        throw new Exception("Operador inválido: " . $operador);
                }

                // Cria a questão e adiciona ao array



                // Ajustar a dificuldade com base na categoria
                /***
                  if (!self::verificarDificuldade($resultado, $categoria)) {
                    continue 2;
                } */

                $questoes[] = new Questao([
                    'questao_texto' => self::gerarTextoQuestao($n1, $n2, $operador),
                    'item1' => $n1,
                    'item2' => $n2,
                    'metodo_id' => $operador_id,
                    'resposta' => $resultado,
                    'pontuacao' => self::calcularPontuacao($categoria),
                    'categoria_id' => self::definirCategoriaId($categoria),
                    'ativo' => true
                ]);
            }
        }

        return $questoes;
    }

    private static function gerarTextoQuestao($n1, $n2, $operador)
    {
        if (strcasecmp($operador, self::OPERADOR_SOMA) == 0) {
            return "Qual o resultado da soma de: $n1 + $n2";
        }
        if (strcasecmp($operador, self::OPERADOR_DIVISAO) == 0) {
            return "Qual o resultado da divisão de: $n1 / $n2";
        }
        if (strcasecmp($operador, self::OPERADOR_MULTIPLICACAO) == 0) {
            return "Qual o resultado da multiplicação de: $n1 X $n2";
        }
        if (strcasecmp($operador, self::OPERADOR_SUBTRACAO) == 0) {
            return "Qual o resultado da subtração de: $n1 - $n2";
        }
    }


    private static function retornaID_operador($operadores, $operador_desejado)
    {
        // Verifica se a resposta foi bem-sucedida
        if ($operadores) {
            foreach ($operadores  as $operador) {

                // Verifica se o método corresponde ao desejado
                if ($operador['metodo'] == $operador_desejado) {
                    // Retorna o ID e o nome do método
                    return  $operador['id'];
                }
            }
        }
        // Caso o operador desejado não seja encontrado, retorna null
        return null;
    }



    /**
     * Verifica se a dificuldade da questão está de acordo com a categoria.
     *
     * @param mixed $resultado Resultado da operação
     * @param string $categoria Categoria da dificuldade
     * @return bool
     */
    private static function verificarDificuldade($resultado, $categoria)
    {
        switch ($categoria) {
            case self::CATEGORIA_ALFABETIZACAO:
                return $resultado <= 20; // Exemplo de dificuldade
            case self::CATEGORIA_INICIANTE:
                return $resultado > 20 && $resultado <= 100; // Exemplo de dificuldade
            case self::CATEGORIA_INTERMEDIARIO:
                return $resultado > 100 && $resultado < 1000;
            case self::CATEGORIA_AVANCADO:
                return $resultado >= 1000 && $resultado < 10000;
            case self::CATEGORIA_SUPER:
                return $resultado >= 1000;
            default:
                throw new Exception("Categoria inválida: " . $categoria);
        }
    }

    /**
     * Define a pontuação com base na categoria.
     *
     * @param string $categoria Categoria da dificuldade
     * @return int
     */
    private static function calcularPontuacao($categoria)
    {
        switch ($categoria) {
            case self::CATEGORIA_ALFABETIZACAO:
                return 10;
            case self::CATEGORIA_INICIANTE:
                return 15;
            case self::CATEGORIA_INTERMEDIARIO:
                return 20;
            case self::CATEGORIA_AVANCADO:
                return 25;
            case self::CATEGORIA_SUPER:
                return 30;
            default:
                return 0;
        }
    }

    /**
     * Define o ID da categoria com base na categoria.
     *
     * @param string $categoria Categoria da dificuldade
     * @return int
     */
    private static function definirCategoriaId($categoria)
    {
        // Aqui você deve retornar o ID da categoria correspondente com base no nome da categoria.
        // Este é apenas um exemplo. Você pode implementar a lógica real conforme necessário.
        switch ($categoria) {
            case self::CATEGORIA_ALFABETIZACAO:
                return 1;
            case self::CATEGORIA_INICIANTE:
                return 2;
            case self::CATEGORIA_INTERMEDIARIO:
                return 3;
            case self::CATEGORIA_AVANCADO:
                return 4;
            case self::CATEGORIA_SUPER:
                return 5;
            default:
                return 0;
        }
    }
}
