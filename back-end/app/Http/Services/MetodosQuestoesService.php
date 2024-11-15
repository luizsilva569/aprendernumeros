<?php

namespace App\Http\Services;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\Response;
use App\Http\Api_core\Sucesso;
use App\Models\AlteracaoMetodoQuestoes;
use App\Models\MetodosQuestoes;
use Illuminate\Support\Facades\DB;

class MetodosQuestoesService
{

    private static function validaRequest($request)
    {
        //valida os dados, se houver erro vai para o catch
        $rules = [
            'metodo' => 'required|string|max:255',
            'usuario_logado' =>'required'
        ];

        return $request->validate($rules);
    }

    private static function validaAlteracoes($request, MetodosQuestoes $model, bool $isCreation=false)
    {
        $alteracoes = [];
        $contador = 0;
        $id_usuario_alterou = $request->usuario_logado;

        //verifica se é novo registro ou se é diferente os novos dados dos dados já no bd..
        //em ambos os casos entra no if...
        if ($isCreation || $model->MetodosQuestoes != $request->MetodosQuestoes) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'metodo',
                //se for criação dá null ao valor, pois a model que recebe aqui em cima é a nova model
                //que acabou de ser criada no BD.
                'valor_anterior' => $isCreation ? null : $model->MetodosQuestoes,
                'valor_novo' => $request->nome
            ];
        }


        ////Nao mexer aqui....
        // Salvando as alterações na tabela 'de alterações ///// nao mexer'
        if ($contador > 0) {
            foreach ($alteracoes as $alteracao) {
                AlteracaoMetodoQuestoes::create([
                    'metodo_id' => $model->id,
                    'campo' => $alteracao['campo'],
                    'valor_anterior' => $alteracao['valor_anterior'],
                    'valor_novo' => $alteracao['valor_novo'],
                    'usuario_alterou' => $id_usuario_alterou
                ]);
            }
        }
        return  $alteracoes;
    }


    public static function novoMetodoQuestoes($request)
    {
        try {
            $dadosValidados = self::validaRequest($request);

            DB::beginTransaction();
            $MetodosQuestoes = MetodosQuestoes::create($dadosValidados);
            //envia para a criaçao dos dados no altera BD

            self::validaAlteracoes($request, $MetodosQuestoes, true);
            DB::commit();
            return Response::jsonEnum(Sucesso::INSERIDO_COM_SUCESSO, null, $MetodosQuestoes);

        } catch (\Illuminate\Validation\ValidationException $e) {
            //lida com os erros de validação... converte em array e trata ele
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);

        } catch (\Exception $e) {
            DB::rollBack();
            // Lida com outros erros
            $erros = [$e->getMessage()];
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function atualizarMetodoQuestoes( $request)
    {
        try {
            //passa o id para ignorar a validação de email...
            $dadosValidados = self::validaRequest($request, $request->id);
            // Encontra o usuário pelo ID
            $MetodoQuestoes = MetodosQuestoes::find($request->id);

            if (!$MetodoQuestoes) {
                $erros = ["Registro não encontrado"];
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            //valida as alterações
            $alteracoes = self::validaAlteracoes($request, $MetodoQuestoes, $request->usuario_logado);
            if (empty($alteracoes)) { //não processa se os dados não houverem alterações.
                $erros = ["Os dados recebidos são idênticos aos atuais."];
                return Response::jsonEnum(Erros::ERRO_ALTERAR_DADOS, $erros);
            }

            // Atualiza os dados do usuário
            $MetodoQuestoes->update($dadosValidados);

            // Adiciona as alterações ao objeto do usuário
            $MetodoQuestoes->alteracoes = $alteracoes;

            return Response::jsonEnum(Sucesso::ATUALIZADO_COM_SUCESSO, null, $MetodoQuestoes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Lida com erros de validação
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function pesquisarMetodosQuestoesPorID($id)
    {
        try {
            // Encontra o usuário pelo ID se o cadastro estiver ativo
            $MetodoQuestoes = MetodosQuestoes::where('id', $id)
                ->where("ativo", true)
                ->first();

            if (!$MetodoQuestoes) {
                $erros[] = "Registro não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, $MetodoQuestoes);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function pesquisarMetodosQuestoes($request)
    {
        try {

            // Cria uma instância da consulta
            $query = MetodosQuestoes::query();

            // Verifica se o parâmetro
            if ($request->has('id')) {
                $id = $request->input('id');
                $query->where('id', $id);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('metodo')) {
                $metodo = strtolower($request->input('metodo'));
                $query->whereRaw('LOWER(metodo) LIKE ?', ["%{$metodo}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('metodoinicio')) {
                $metodo = strtolower($request->input('metodoinicio'));
                $query->whereRaw('LOWER(metodo) LIKE ?', ["{$metodo}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('metodoigual')) {
                $metodo = strtolower($request->input('metodoigual'));
                $query->whereRaw('LOWER(metodo) = ?', [$metodo]);
            }

            $MetodosQuestoes = $query->get();

            if (!$MetodosQuestoes) {
                $erros[] = "Nenhum registro encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, null, $MetodosQuestoes);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function deleteMetodosQuestoes($id)
    {
        try {
            $metodo = MetodosQuestoes::find($id);

            if (!$metodo) {
                $erros[] = "Registro não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            $metodo->delete();
            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::EXCLUIDO_COM_SUCESSO, null, $metodo);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }
}
