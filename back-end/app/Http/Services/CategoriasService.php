<?php

namespace App\Http\Services;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\Response;
use App\Http\Api_core\Sucesso;
use App\Models\AlteracaoCategoria;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class CategoriasService
{

    private static function validaRequest($request)
    {
        //valida os dados, se houver erro vai para o catch
        $rules = [
            'categoria' => 'required|string|max:255',
            'usuario_logado' =>'required'
        ];

        return $request->validate($rules);
    }

    private static function validaAlteracoes($request, Categoria $model, bool $isCreation=false)
    {
        $alteracoes = [];
        $contador = 0;
        $id_usuario_alterou = $request->usuario_logado;

        //verifica se é novo registro ou se é diferente os novos dados dos dados já no bd..
        //em ambos os casos entra no if...
        if ($isCreation || $model->categoria != $request->categoria) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'categoria',
                //se for criação dá null ao valor, pois a model que recebe aqui em cima é a nova model
                //que acabou de ser criada no BD.
                'valor_anterior' => $isCreation ? null : $model->categoria,
                'valor_novo' => $request->nome
            ];
        }

        // Salvando as alterações na tabela 'alteracoes_usuarios'
        if ($contador > 0) {
            foreach ($alteracoes as $alteracao) {
                AlteracaoCategoria::create([
                    'categoria_id' => $model->id,
                    'campo' => $alteracao['campo'],
                    'valor_anterior' => $alteracao['valor_anterior'],
                    'valor_novo' => $alteracao['valor_novo'],
                    'usuario_alterou' => $id_usuario_alterou
                ]);
            }
        }
        return  $alteracoes;
    }


    public static function novaCategoria($request)
    {
        try {
            $dadosValidados = self::validaRequest($request);

            DB::beginTransaction();
            $categoria = Categoria::create($dadosValidados);
            //envia para a criaçao dos dados no altera BD
            self::validaAlteracoes($request, $categoria, true);
            DB::commit();
            return Response::jsonEnum(Sucesso::INSERIDO_COM_SUCESSO, null, $categoria);

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

    public static function atualizarCategoria($id, $request, $usuario_logado)
    {
        try {
            //passa o id para ignorar a validação de email...
            $dadosValidados = self::validaRequest($request, $request->id);
            // Encontra o usuário pelo ID
            $categoria = Categoria::find($id);

            if (!$categoria) {
                $erros = ["categoria não encontrada"];
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            //valida as alterações
            $alteracoes = self::validaAlteracoes($request, $categoria, $usuario_logado);
            if (empty($alteracoes)) { //não processa se os dados não houverem alterações.
                $erros = ["Os dados recebidos são idênticos aos atuais."];
                return Response::jsonEnum(Erros::ERRO_ALTERAR_DADOS, $erros);
            }

            // Atualiza os dados do usuário
            $categoria->update($dadosValidados);

            // Adiciona as alterações ao objeto do usuário
            $categoria->alteracoes = $alteracoes;

            return Response::jsonEnum(Sucesso::ATUALIZADO_COM_SUCESSO, null, $categoria);
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

    public static function pesquisarCategoriaPorID($id)
    {
        try {
            // Encontra o usuário pelo ID se o cadastro estiver ativo
            $categoria = Categoria::where('id', $id)
                ->where("ativo", true)
                ->first();

            if (!$categoria) {
                $erros[] = "Categoria não encontrada";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, $categoria);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function pesquisarCategorias($request)
    {
        try {

            // Cria uma instância da consulta
            $query = Categoria::query();

            // Verifica se o parâmetro
            if ($request->has('id')) {
                $id = $request->input('id');
                $query->where('id', $id);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('categoria')) {
                $categoria = strtolower($request->input('categoria'));
                $query->whereRaw('LOWER(nome) LIKE ?', ["%{$categoria}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('categoriainicio')) {
                $categoria = strtolower($request->input('categoriainicio'));
                $query->whereRaw('LOWER(categoria) LIKE ?', ["{$categoria}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('categoriaigual')) {
                $categoriaigual = strtolower($request->input('categoriaigual'));
                $query->whereRaw('LOWER(categoria) = ?', [$categoriaigual]);
            }

            //  echo  $query->toSql();
            $categorias = $query->get();

            if (!$categorias) {
                $erros[] = "Nenhum registro encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, null, $categorias);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }

    public static function deleteCategoria($id)
    {
        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                $erros[] = "Registro não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            $categoria->delete();
            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::EXCLUIDO_COM_SUCESSO, null, $categoria);
        } catch (\Exception $e) {
            // Lida com outros erros
            $erros[] = $e->getMessage();
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros);
        }
    }
}
