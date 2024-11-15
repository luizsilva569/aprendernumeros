<?php

namespace App\Http\Services;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\Response;
use App\Http\Api_core\Sucesso;
use App\Models\QuestoesRespondidas;
use App\Models\Questao;
use Illuminate\Support\Facades\DB;

class QuestoesRespondidasService
{
    private static function validaRequest($request)
    {
        //valida os dados, se houver erro vai para o catch
        $rules = [
            'token_questao' => 'required|string',
            'resposta' => 'required|integer',
            'usuario_logado' => 'required',
        ];
        return $request->validate($rules);
    }


    //soma a pontuação do usuário
    public static function getPontuacaoPorUsuario($usuario_id)
    {
        //função para calcular o tempo gasto na requisição
        $inicio = microtime(true);

        //busca a pontuação do usuario
        $pontuacao = self::getSomentePontuacaoTotalUsuario($usuario_id);

        //cria um objeto com a pontuação
        $objPontuacao =
            [
                'pontuacao_usuario' => $pontuacao
            ];

        //calcula o tempo gasto para buscar o resultado
        $tempoGasto = microtime(true) - $inicio;
        return Response::jsonEnum(Sucesso::EXECUTADO_COM_SUCESSO, null, $objPontuacao, $tempoGasto);
    }

    //Função que retorna uma resposta com o valor numerico do total de pontos do usuario
    public static function getSomentePontuacaoTotalUsuario($usuario_id)
    {
        //busca a pontuação no bd...
        $pontuacao = QuestoesRespondidas::where('usuario_id', $usuario_id)
            ->sum('pontuacao');

        return $pontuacao;
    }

    public static function novaQuestaoRespondida($request)
    {
        try {
            // Valida os dados da requisição
            $dadosValidados = self::validaRequest($request);

            // Busca a questão no banco de dados
            $questaoBD = Questao::where('token_questao', $request->token_questao)->firstOrFail();

            // Verifica se a resposta está correta
            $acertou = ($request->resposta == $questaoBD->resposta);

            //vou comentar esse código, deixarei pois ele poderá ser muito util futuramente.
            // Verifica se já existe uma questão respondida com o mesmo usuário e token, mas que não esteja sendo refazendo
           /* if (!$request->has('refazendo') || $request->refazendo == false) {
                $questaoExistente = QuestoesRespondidas::where('usuario_id', $request->usuario_logado)
                    ->where('token_questao', $request->token_questao)
                    ->first();

                if ($questaoExistente) {
                    $erros = ["Questão já respondida pelo usuário."];
                    return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);
                }
            }
            */
            $pontuacaoTotal = self::getSomentePontuacaoTotalUsuario($request->usuario_logado) + ($acertou ? $questaoBD->pontuacao : 0);

            // Adiciona os dados necessários para a inserção
            $dadosValidados['acertou'] = $acertou;
            $dadosValidados['resposta_usuario'] = $request->resposta;
            $dadosValidados['resposta_correta'] = $questaoBD->resposta;
            $dadosValidados['questao_id'] = $questaoBD->id;
            $dadosValidados['usuario_id'] = $request->usuario_logado;
            $dadosValidados['pontuacao'] = $acertou ? $questaoBD->pontuacao : 0;
            //somente pontua se acertar a questao.

            DB::beginTransaction();
            // Cria um novo registro na tabela questoes_respondidas
            $questaoRespondida = new QuestoesRespondidas();
            $questaoRespondida->fill($dadosValidados);
            $questaoRespondida->save();

            // Comita a transação
            DB::commit();

            //Traduz os dados para o frontEnd
            $response['acertou'] = $acertou? :"Sim";"Não";
            $response['Sua resposta'] = $request->resposta;
            $response['Resposta esperada'] = $questaoBD->resposta;
            $response['Pontuação adicionada'] = $acertou ? $questaoBD->pontuacao : 0 . " pontos";
            $response['Sua pontuação total'] = $pontuacaoTotal . " pontos";

            // Retorna a resposta de sucesso
            return Response::jsonEnum(Sucesso::EXECUTADO_COM_SUCESSO, null, $response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Lida com os erros de validação
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);
        } catch (\Exception $e) {
            // Rollback da transação em caso de erro
            DB::rollBack();
            // Lida com outros erros
            $erros = [$e->getMessage()];
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }


    public static function pesquisarQuestaoPorID($id)
    {
        try {

            // Encontra o usuário pelo ID se o cadastro estiver ativo
            $questao = QuestoesRespondidas::where('id', $id)
                ->where("ativo", true)
                ->first();

            if (!$questao) {
                $erros[] = "Registro não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, $questao);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }
    public static function pesquisarQuestoesRespondidas($request)
    {
        try {

            // Cria uma instância da consulta
            $query = Questao::query();

            // Verifica se o parâmetro
            if ($request->has('id')) {
                $id = $request->input('id');
                $query->where('id', $id);
            }

            if ($request->has('token_questao')) {
                $questao = strtolower($request->input('token_questao'));
                $query->whereRaw('LOWER(token_questao) LIKE ?', ["{$questao}"]);
            }


            if ($request->has('questao_id')) {
                $questao = strtolower($request->input('questao_id'));
                $query->whereRaw('LOWER(questao_id) LIKE ?', ["{$questao}"]);
            }

            if ($request->has('usuario_id')) {
                $questao = strtolower($request->input('usuario_id'));
                $query->whereRaw('LOWER(usuario_id) = ?', [$questao]);
            }

            if ($request->has('pontuacao')) {
                $questao = strtolower($request->input('pontuacao'));
                $query->whereRaw('LOWER(pontuacao) = ?', [$questao]);
            }

            // echo $query->toSql();

            $questoes = $query->get();

            if (!$questoes) {
                $erros[] = "Nenhum registro encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, null, $questoes);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function deleteQuestao($id)
    {
        try {
            $questao = QuestoesRespondidas::find($id);

            if (!$questao) {
                $erros[] = "Registro não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            $questao->delete();
            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::EXCLUIDO_COM_SUCESSO, null, $questao);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }
}
