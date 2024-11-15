<?php

namespace App\Http\Seguranca;

use App\Http\Seguranca\AutenticacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\padrao\Usuario;
use App\Http\Api_core\Response;
use App\Http\Api_core\Erros;
use App\Http\Api_core\Sucesso;

class Login
{
    public function fazerLogin(Request $request)
    {
        Log::info('Conteúdo do request:', $request->all());
        $ipUsuario = $request->ip();
        $usuario = $request->input('email');
        $senha = $request->input('senha');

        // Verifica se ambos os campos foram preenchidos
        if (!$usuario || !$senha) {
            Log::error('Login ou senha não recebidos: '  . json_encode($request->all()) . ' IP do usuário: ' . $ipUsuario);
            return Response::jsonEnum(Erros::LOGIN_OU_SENHA_NAO_RECEBIDOS, null);
        }
        try {
            // pega os dados
            $email = $request->input('email');
            $senha = $request->input('senha');
            // Exemplo de credenciais (em um sistema real, busque do banco de dados)
            $usuario_encontrado = self::pesquisarUsuario_ParaLogin($email);

            // Verifica se o usuário existe
            if (!$usuario_encontrado) {
                Log::error('Usuário não encontrado', [
                    'request' => $request->all(),
                    'IP do usuário' => $ipUsuario
                ]);
                 return Response::jsonEnum(Erros::LOGIN_OU_SENHA_INVALIDOS, null);
            }


            // Verifica se a senha está correta
            if (Hash::check($senha, $usuario_encontrado->senha)) {
                // Usuário autenticado com sucesso, gerar token
                $autenticacao = new AutenticacaoService();
                $token = $autenticacao->gerarToken($usuario_encontrado->id); // Usando o ID do usuário encontrado
                Log::error('login correto');

                //Dados retornados no objeto do json
                $objeto_retorno = [
                    'nome_usuario' => $usuario_encontrado->nome,
                    'token' => $token
                ];

                return Response::jsonEnum(Sucesso::LOGIN_COM_SUCESSO, null, $objeto_retorno);
            } else {
                Log::error('Dados invalidos no login: ' . json_encode($request->all()) . 'IP do usuário: ' . $ipUsuario);

                //apresenta o mínimo de informações possíveis.
                return response()->json([
                    'status' => 401,
                    'mensagem' => 'Dados inválidos'
                ], 401); // Código de erro 401 para Unauthorized

            }
        } catch (\Exception $e) {
            // Lida com outros erros
            Log::error('Erro ao pesquisar usuário para login: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }

    private static function pesquisarUsuario_ParaLogin($email)
    {
        try {
            // Cria uma instância da consulta
            $usuario_encontrado = Usuario::where('email', $email)
                ->where('ativo', true) // Adiciona a condição de que o usuário deve estar ativo
                ->first(); // Retorna o primeiro resultado ou null

            if ($usuario_encontrado) {
                $usuario_encontrado->makeVisible('senha');
            }

            return $usuario_encontrado; // Retorna o usuário encontrado (ou null se não encontrado)
        } catch (\Exception $e) {
            // Lida com outros erros
            Log::error('Erro ao realizar login: ' . $e->getMessage());
            return Response::jsonEnum(Erros::ERRO_INTERNO, null);
        }
    }
}
