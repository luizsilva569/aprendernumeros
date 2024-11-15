<?php

namespace App\Http\Services;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\GeradorQuestoesMatematicas;
use App\Http\Api_core\Response;
use App\Http\Api_core\TraducaoErros;
use App\Http\Api_core\Sucesso;
use App\Models\AlteracaoQuestoes;
use App\Models\Questao;
use App\Models\QuestoesRespondidas;
use App\Models\padrao\Usuario;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\IsTrue;

class QuestoesService
{
    private static function validaRequest($request)
    {
        //valida os dados, se houver erro vai para o catch
        $rules = [
            'questao_texto' => 'required|string',
            'item1' => 'required|integer',
            'item2' => 'nullable|integer',
            'item3' => 'nullable|integer',
            'metodo_id' => 'required|integer',
            'resposta' => 'required|integer',
            'pontuacao' => 'required|integer',
            'categoria_id' => 'required|integer',
            'usuario_logado' => 'required|integer',
        ];
        return $request->validate($rules);
    }

    private static function validaAlteracoes($request, Questao $model, bool $isCreation = false)
    {
        $alteracoes = [];
        $contador = 0;
        $id_usuario_alterou = $request->usuario_logado;

        //verifica se é novo registro ou se é diferente os novos dados dos dados já no bd..
        //em ambos os casos entra no if...
        //se for criação dá null ao valor, pois a model que recebe aqui em cima é a nova model
        //que acabou de ser criada no BD.
        if ($isCreation || $model->metodo != $request->metodo) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'metodo',
                'valor_anterior' => $isCreation ? null : $model->metodo,
                'valor_novo' => $request->metodo
            ];
        }

        if ($isCreation || $model->item1 != $request->item1) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'item1',
                'valor_anterior' => $isCreation ? null : $model->item1,
                'valor_novo' => $request->item1
            ];
        }
        if ($isCreation || $model->item2 != $request->item2) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'item2',
                'valor_anterior' => $isCreation ? null : $model->item2,
                'valor_novo' => $request->item2
            ];
        }

        if ($isCreation || $model->item3 != $request->item3) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'item3',
                'valor_anterior' => $isCreation ? null : $model->item3,
                'valor_novo' => $request->item3
            ];
        }
        if ($isCreation || $model->questao_texto != $request->questao_texto) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'questao_texto',
                'valor_anterior' => $isCreation ? null : $model->questao_texto,
                'valor_novo' => $request->questao_texto
            ];
        }
        if ($isCreation || $model->questao_texto != $request->questao_texto) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'questao_texto',
                'valor_anterior' => $isCreation ? null : $model->questao_texto,
                'valor_novo' => $request->questao_texto
            ];
        }
        if ($isCreation || $model->resposta != $request->resposta) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'resposta',
                'valor_anterior' => $isCreation ? null : $model->resposta,
                'valor_novo' => $request->resposta
            ];
        }
        if ($isCreation || $model->pontuacao != $request->pontuacao) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'pontuacao',
                'valor_anterior' => $isCreation ? null : $model->pontuacao,
                'valor_novo' => $request->pontuacao
            ];
        }

        if ($isCreation || $model->questao_id != $request->questao_id) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'questao_id',
                'valor_anterior' => $isCreation ? null : $model->questao_id,
                'valor_novo' => $request->questao_id
            ];
        }

        // Salvando as alterações na tabela 'alteracoes_usuarios'
        if ($contador > 0) {
            foreach ($alteracoes as $alteracao) {
                AlteracaoQuestoes::create([
                    'questao_id' => $model->id,
                    'campo' => $alteracao['campo'],
                    'valor_anterior' => $alteracao['valor_anterior'],
                    'valor_novo' => $alteracao['valor_novo'],
                    'usuario_alterou' => $id_usuario_alterou
                ]);
            }
        }
        return  $alteracoes;
    }

    //define o token da questão para que seja enviado este em vez do id para o usuario,
    //pois o id a pessoa poderia enviar um post com o proximo id, assim responder
    //outras quetoes sem autorização... com o token a pessoa nao consegue descobrir o proximo
    private static function getTokenQuestao()
    {
        return uniqid('1quest', true);
    }

    public static function getQuestao($request)
    {
        // Encontra o nível do usuário para pesquisar uma nova questão dentro do mesmo nível
        $usuario_id = $request->usuario_logado;
        $usuario = Usuario::where('id', $usuario_id)->where("ativo", true)->first();

        if (!$usuario) {
            $erros[] = "Usuário logado não encontrado.";
            return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
        }

        // Obtém o nível do usuário
        $nivel = $usuario->nivel;

        // Verifica se uma categoria foi passada na requisição
        $categoria_id = $request->has('categoria_id') ? $request->categoria_id : null;

        // Obtém os IDs das questões já respondidas pelo usuário
        $questoesRespondidas = [];
       /*
        $questoesRespondidas = QuestoesRespondidas::where('usuario_id', $usuario_id)
            ->where('acertou', true)
            ->pluck('questao_id')
            ->toArray();
*/
        // Pesquisa uma nova questão por nível ou categoria
        $nova_questao = Questao::when($categoria_id, function ($query, $categoria_id) {
            // Se uma categoria for passada, filtra pela categoria
            return $query->where('categoria_id', $categoria_id);
        }, function ($query) use ($nivel) {
            // Caso contrário, filtra pelo nível do usuário
            return $query->where('categoria_id', $nivel);
        })
            ->whereNotIn('id', $questoesRespondidas)
            ->inRandomOrder()
            ->first();

        if (!$nova_questao) {
            $erros[] = "Nenhuma nova questão cadastrada está disponível para o usuário.";
            return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
        }

        return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, null, $nova_questao);
    }

    public static function gerarQuestoesAutomaticas(
        $request,
        $min_item1,
        $max_item1,
        $min_item2,
        $max_item2,
        $operador,
        $categoria
    ) {
        $inicio = microtime(true);
        $erros = [];
        $sucessos = [];

        $questoes = GeradorQuestoesMatematicas::gerarQuestoes(
            $min_item1,
            $max_item1,
            $min_item2,
            $max_item2,
            $operador,
            $categoria
        );
        $qdeQuestoes = count($questoes);
        if ($qdeQuestoes < 1) {
            $erros[] = "Nenhuma questão foi gerada.";
            return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros, $sucessos);
        }

        $contador = 0;
        try {
            // Gera as questões
            foreach ($questoes as $questao) {
                try {
                    $contador++;
                    DB::beginTransaction();

                    // Cria uma nova instância de Questao
                    $novaQuestao = new Questao();

                    // Define os atributos da nova questão
                    $novaQuestao->questao_texto = $questao->questao_texto;
                    $novaQuestao->item1 = $questao->item1;
                    $novaQuestao->item2 = $questao->item2;
                    $novaQuestao->item3 = $questao->item3;
                    $novaQuestao->metodo_id = $questao->metodo_id;
                    $novaQuestao->resposta = $questao->resposta;
                    $novaQuestao->pontuacao = $questao->pontuacao;
                    $novaQuestao->categoria_id = $questao->categoria_id;
                    $novaQuestao->token_questao = self::getTokenQuestao();

                    // Salva a nova questão no banco de dados
                    $novaQuestao->save();

                    // Busca a questão salva usando o token
                    $questaoSalva = Questao::where('token_questao', $novaQuestao->token_questao)->first();

                    // Verifica se a questão foi encontrada
                    if ($questaoSalva) {
                        // Passa o usuário logado para a própria questão
                        $novaQuestao->usuario_logado =  $request->usuario_logado;
                        // Valida alterações
                        self::validaAlteracoes($novaQuestao, $questaoSalva, true);

                        $sucessos[] = "Item $contador de $qdeQuestoes. " . $questao->questao_texto;
                    } else {
                        $erros[] = "Não foi possível encontrar a questão salva.";
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $traducaoErro = TraducaoErros::traduzirErro($e->getMessage());
                    $erros[] = "Item $contador de $qdeQuestoes. " . $traducaoErro;
                }
            }

            $fim = microtime(true);
            $tempo_gasto = $fim - $inicio;

            // Retorna a resposta com sucesso
            return Response::jsonEnum(Sucesso::EXECUTADO_COM_SUCESSO, $erros, $sucessos, $tempo_gasto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Lida com erros de validação
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            $fim = microtime(true);
            $tempo_gasto = $fim - $inicio;
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros, $sucessos, $tempo_gasto);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lida com outros erros
            $erros[] = $e->getMessage();
            $fim = microtime(true);
            $tempo_gasto = $fim - $inicio;
            return Response::jsonEnum(Erros::ERRO_INTERNO, $erros, $sucessos, $tempo_gasto);
        }
    }

    public static function novaQuestao($request)
    {
        try {
            $dadosValidados = self::validaRequest($request);

            DB::beginTransaction();
            $dadosValidados->token_questao = self::getTokenQuestao();

            $questao = Questao::create($dadosValidados);
            //envia para a criaçao dos dados no altera BD
            self::validaAlteracoes($request, $questao, true);
            DB::commit();
            return Response::jsonEnum(Sucesso::INSERIDO_COM_SUCESSO, null, $questao);
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


    public static function atualizarQuestao($request)
    {
        try {
            //passa o id para ignorar a validação de email...
            $dadosValidados = self::validaRequest($request);
            // Encontra o usuário pelo ID
            $registroAtual = Questao::find($request->id);

            if (!$registroAtual) {
                $erros = ["Registro não encontrado"];
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            //valida as alterações
            $alteracoes = self::validaAlteracoes($request, $registroAtual);
            if (empty($alteracoes)) { //não processa se os dados não houverem alterações.
                $erros = ["Os dados recebidos são idênticos aos atuais."];
                return Response::jsonEnum(Erros::ERRO_ALTERAR_DADOS, $erros);
            }

            // Atualiza os dados do usuário
            $registroAtual->update($dadosValidados);

            // Adiciona as alterações ao objeto do usuário
            $registroAtual->alteracoes = $alteracoes;

            return Response::jsonEnum(Sucesso::ATUALIZADO_COM_SUCESSO, null, $registroAtual);
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

    public static function pesquisarQuestaoPorID($id)
    {
        try {
            // Encontra o usuário pelo ID se o cadastro estiver ativo
            $questao = Questao::where('id', $id)
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
    public static function pesquisarQuestoes($request)
    {
        try {

            // Cria uma instância da consulta
            $query = Questao::query();

            // Verifica se o parâmetro
            if ($request->has('id')) {
                $id = $request->input('id');
                $query->where('id', $id);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('questao')) {
                $questao = strtolower($request->input('questao'));
                $query->whereRaw('LOWER(questao) LIKE ?', ["%{$questao}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('questaoinicio')) {
                $questao = strtolower($request->input('questaoinicio'));
                $query->whereRaw('LOWER(questao) LIKE ?', ["{$questao}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('questaoigual')) {
                $questaoigual = strtolower($request->input('questaoigual'));
                $query->whereRaw('LOWER(questao) = ?', [$questaoigual]);
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
            $questao = Questao::find($id);

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
