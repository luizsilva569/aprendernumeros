<?php

namespace App\Http\Seguranca;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use App\Http\Api_core\Response;
use App\Http\Api_core\Erros;


class AutenticacaoService
{
    // gera o segredo do token conforme o usuario
    private static function segredoToken()
    {
        return "key_cheow_cheow_";
    }

    // gera o segredo do token conforme o usuario
    private static function metodoCriptografia()
    {
        return 'HS256';
    }


    //gera a data e hora em que o token irá expirar
    private static function tempoExpirarToken()
    {
        $dias_ate_expirar = 30;
        $horas_ate_expirar = 1;              // irá somar com os minutos
        $minutos_ate_expirar = 0;           //soma-se ao tempo de horas
        $segundos_ate_expirar = 30;       // soma-se ao tempo acima
        $tempo_em_segundos = (
            $segundos_ate_expirar +
            ($minutos_ate_expirar * 60) +
            ($horas_ate_expirar * 3600) +
            ($dias_ate_expirar * 86400)

        );

        //retorna agora + as horas até expirar
        return  time() + $tempo_em_segundos;
    }

    // Gera o token
    public static function gerarToken($usuario_id)
    {
        $chave = self::segredoToken();
        $metodo_criptografia = self::metodoCriptografia();

        // Define os dados que serão incluídos no token
        $dados = [
            'iss' => "gandela.cheow",              // Emissor do token
            'aud' => "user_gandela",               // Audiência (quem pode usar o token)
            'iat' => time(),                       // Timestamp de emissão
            'exp' => self::tempoExpirarToken(),    // Expira conforme o inserido na função
            'usuario_id' => $usuario_id             // Dados do usuário
        ];

        // Gera o token JWT usando o algoritmo HS256
        return JWT::encode($dados, $chave, $metodo_criptografia);
    }

    // Valida o token enviado
    public static function validarTokenEnviado($request)
    {
        $token = $request->bearerToken(); // Obtém o token do cabeçalho Authorization
        if (!$token) {
            $erros[] = "Token não recebido.";
            return Response::jsonEnum(Erros::TOKEN_NAO_RECEBIDO, $erros);
        }

        try {
            // Decodifica o token JWT
            $chave = self::segredoToken();   // Usando a chave utilizada para codificar
            $metodo_criptografia = self::metodoCriptografia();
            $credenciais = JWT::decode($token, new Key($chave, $metodo_criptografia));

            //passa os dados do token para a request..
            $request->merge(
                [
                    'usuario_logado' => $credenciais->usuario_id
                ]
            );

        } catch (Exception $e) {
            $erros[] = "Token inválido ou expirado.";
            return Response::jsonEnum(Erros::TOKEN_INVALIDO, $erros);
        }
    }
}
