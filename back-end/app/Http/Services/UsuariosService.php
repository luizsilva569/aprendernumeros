<?php

namespace App\Http\Services;

use App\Http\Api_core\Response;
use App\Http\Api_core\erros;
use App\Http\Api_core\Sucesso;
use App\Models\padrao\Usuario;
use App\Http\Api_core\FuncoesGlobais;
use App\Models\AlteracaoUsuarios;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuariosService
{

    private  function validaRequest($request, $id = null)
    {
        //valida os dados, se houver erro vai para o catch
        $rules = [
            'nome' => [
                'required',
                'max:255',
                'min: 4',
                Rule::unique('usuarios')->ignore($id), // Ignora o ID do usuário atual
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios')->ignore($id), // Ignora o ID do usuário atual
            ],
            'senha' => 'required|string|min:6'

        ];

        return $request->validate($rules);
    }

    private  function validaAlteracoes($request, Usuario $usuario, bool $isCreation = false)
    {

        $alteracoes = [];
        $contador = 0;
        $id_usuario_alterou = $request->usuario_logado ?? 1;

        // Verificar e adicionar alterações para cada campo
        if ($isCreation || $usuario->nome != $request->nome) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'nome',
                'valor_anterior' => $isCreation ? null : $usuario->nome,
                'valor_novo' => $request->nome
            ];
        }

        if ($isCreation || $usuario->email != $request->email) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'email',
                'valor_anterior' => $isCreation ? null : $usuario->email,
                'valor_novo' => $request->email
            ];
        }

        if ($isCreation || $usuario->dtNascimento != Carbon::parse($request->input('dtNascimento'))->startOfDay()) {
            $contador += 1;
            $alteracoes[] = [
                'campo' => 'dtNascimento',
                'valor_anterior' => $isCreation ? null : $usuario->dtNascimento,
                'valor_novo' => $request->input('dtNascimento')
            ];
        }

        // Salvando as alterações na tabela 'alteracoes_usuarios'
        if ($contador > 0) {
            foreach ($alteracoes as $alteracao) {
                AlteracaoUsuarios::create([
                    'usuario_id' => $usuario->id,
                    'campo' => $alteracao['campo'],
                    'valor_anterior' => $alteracao['valor_anterior'],
                    'valor_novo' => $alteracao['valor_novo'],
                    'usuario_alterou' => $id_usuario_alterou
                ]);
            }
        }
        return $alteracoes;
    }


    //Trata e cria um novo usuario para o BD com os dados da request
    public  function novoUsuario($request)
    {
        try {
            $dadosValidados = self::validaRequest($request);



            //faz hash da senha
            if (isset($dadosValidados['senha'])) {
                $dadosValidados['senha'] = Hash::make($dadosValidados['senha']);
            }

            DB::beginTransaction();

            // Cria o usuário
            $usuario = Usuario::create($dadosValidados);

            //envia dados para inserção na tabela de alterções..
            self::validaAlteracoes($request, $usuario, true);

            DB::commit();
            return Response::jsonEnum(Sucesso::INSERIDO_COM_SUCESSO, null, $usuario);
        } catch (\Illuminate\Validation\ValidationException $e) {
            //lida com os erros de validação... converte em array e trata ele
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lida com outros erros
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }

    public  function atualizarUsuario($id, $request)
    {
        try {
            //passa o id para ignorar a validação de email...
            $dadosValidados = self::validaRequest($request, $request->id);
            // Encontra o usuário pelo ID
            $usuario = Usuario::find($id);

            if (!$usuario) {
                $erros = ["usuario não encontrado"];
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            //faz hash da senha
            if (isset($dadosValidados['senha'])) {
                $dadosValidados['senha'] = Hash::make($dadosValidados['senha']);
            }

            //valida as alterações
            $alteracoes = self::validaAlteracoes($request, $usuario);
            if (empty($alteracoes)) { //não processa se os dados não houverem alterações.
                $erros = ["Os dados recebidos são idênticos aos atuais."];
                return Response::jsonEnum(Erros::ERRO_ALTERAR_DADOS, $erros);
            }
            // Atualiza os dados do usuário
            $usuario->update($dadosValidados);

            // Adiciona as alterações ao objeto do usuário
            $usuario->alteracoes = $alteracoes;

            return Response::jsonEnum(Sucesso::ATUALIZADO_COM_SUCESSO, null, $usuario);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Lida com erros de validação
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros);
        } catch (\Exception $e) {
            // Lida com outros erros
            Log::error('Erro ao alterar usuário: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }


    public  function pesquisarUsuarioPorID($id)
    {
        try {
            // Encontra o usuário pelo ID se o cadastro estiver ativo
            $usuario = Usuario::where('id', $id)
                ->where("ativo", true)
                ->first();

            if (!$usuario) {
                $erros[] = "Usuário não encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, $usuario);
        } catch (\Exception $e) {
            // Lida com outros erros
            Log::error('Erro ao pesquisar usuário por id: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }

    public  function pesquisarUsuarios($request)
    {
        try {

            // Cria uma instância da consulta
            $query = Usuario::query();
            $query->where('ativo', $request->input('ativo', true)); //marca como ativo = true


            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('id')) {
                $id = $request->input('id');
                $query->where('id', $id);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('nome')) {
                $nome = strtolower($request->input('nome'));
                $query->whereRaw('LOWER(nome) LIKE ?', ["%{$nome}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('nomeinicio')) {
                $nome = strtolower($request->input('nomeinicio'));
                $query->whereRaw('LOWER(nome) LIKE ?', ["{$nome}%"]);
            }

            // Verifica se o parâmetro 'nome' está presente e adiciona a condição à consulta
            if ($request->has('nomeigual')) {
                $nome = strtolower($request->input('nomeigual'));
                $query->whereRaw('LOWER(nome) = ?', [$nome]);
            }

            // Verifica se o parâmetro 'email' está presente e adiciona a condição à consulta
            if ($request->has('email')) {
                $email = strtolower($request->input('email'));
                $query->whereRaw('LOWER(email) LIKE ?', ["%{$email}%"]);
            }
            $usuariosAtivos = $query->get();

            if (!$usuariosAtivos) {
                $erros[] = "Nenhum usuário encontrado";
                return Response::jsonEnum(Erros::DADOS_NAO_ENCONTRADOS, $erros);
            }

            // Retorna uma resposta de sucesso com os dados do usuário
            return Response::jsonEnum(Sucesso::APRESENTADO_COM_SUCESSO, null, $usuariosAtivos);
        } catch (\Exception $e) {
            // Lida com outros erros
            Log::error('Erro ao pesquisar usuários por diversos meios: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }
}
