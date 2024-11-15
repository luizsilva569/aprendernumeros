<?php

namespace App\Http\Services;

use App\Http\Api_core\Erros;
use App\Http\Api_core\Response;
use App\Http\Api_core\Sucesso;
use App\Models\QuestoesRespondidas;
use Illuminate\Support\Facades\DB;

class RankingService
{
    private static function validaRequest($request)
    {
        // Valida os dados, se houver erro, vai para o catch
        $rules = [
            'usuario_logado' => 'required',
        ];
        return $request->validate($rules);
    }

    // Soma a pontuação de todos os usuários
    public static function getRanking($request)
    {
        try {
            // Validação do request
            self::validaRequest($request);

            $usuario_id = $request->usuario_logado;

            // Supondo que a tabela 'usuarios' tenha as colunas 'id' e 'nome'
            $pontuacoes = QuestoesRespondidas::select('usuario_id', 'usuarios.nome', DB::raw('SUM(pontuacao) as total_pontuacao'))
                ->join('usuarios', 'questoes_respondidas.usuario_id', '=', 'usuarios.id')
                ->groupBy('usuario_id', 'usuarios.nome')
                ->orderBy('total_pontuacao', 'desc') // Ordena do maior para o menor
                ->get();

            $ranking = [];
            $sua_posicao = null;
            $sua_pontuacao = 0;  // Inicia a pontuação do usuário em zero
            $posicao = 1; // Inicia a contagem de posições a partir de 1

            foreach ($pontuacoes as $pontuacao) {
                $ranking[] = [
                    'nome' => $pontuacao->nome,
                    'pontuacao' => $pontuacao->total_pontuacao,
                    'posicao' => $posicao // Adiciona a posição ao objeto
                ];

                // Verifica se o usuário atual é o usuário logado
                if ($pontuacao->usuario_id == $usuario_id) {
                    $sua_posicao = $posicao;
                    $sua_pontuacao = $pontuacao->total_pontuacao;
                }

                $posicao++; // Incrementa a posição
            }

            // Retorna a posição e pontuação do usuário logado junto com o ranking completo
            $resultado = [
                'sua_pontuacao' => $sua_pontuacao,
                'sua_posicao' => $sua_posicao,
                'ranking' => $ranking
            ];

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO,null, $resultado);

        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }
}
