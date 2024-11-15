<?php


namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    public function __construct()
    {
        // Middleware para verificar se 'nome_usuario' existe na sessão, senão direciona para login
        $this->middleware(function ($request, $next) {

            // Rotas para excluir da verificação de sessão
            $excecoes = ['login', '', 'home', 'usuario', 'password/reset'];

            // ignora a verificação de sessão
            if (in_array($request->path(), $excecoes)) {
                return $next($request);
            }

            //redefine a rota para o login
            if (!session()->has('nome_usuario')) {
                return redirect('/login');
            }
            return $next($request);
        });
    }
}
