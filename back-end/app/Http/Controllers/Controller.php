<?php

namespace App\Http\Controllers;

use App\Http\Seguranca\AutenticacaoService;
use Illuminate\Routing\Controller as BaseController;

//esse é o controller principal, onde é exportado para os demais controllers..
class Controller extends BaseController
{
    //aqui é validado o token que deve ser enviado para todas as requisições
    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');

        $this->middleware(function ($request, $next) {
            // Valida o token antes de qualquer ação
            $response = AutenticacaoService::validarTokenEnviado($request);

            // Se a validação falhar, retorna a resposta de erro
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                return $response;
            }

            // Se estiver tudo certo, continua com a requisição
            return $next($request);
        });
    }
}
