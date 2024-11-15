<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\UsuariosService;

class UsuarioController
{
    /**
     * Apresenta uma lista com os dados solicitados
     */
    public function index(Request $request)
    {
        return  UsuariosService::pesquisarUsuarios($request);
    }

    /**
     * Salva os dados no banco de dados
     */
    public function store(Request $request)
    {
        $service = new UsuariosService();
        return $service->novoUsuario($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = new UsuariosService();
        return $service->pesquisarUsuarioPorID($id);
    }

    /**
     * Atualiza um cadastro no banco de dados
     * Recebe um id_do_usuarioLogado..
     */
    public function update(Request $request, string $id)
    {
        $service = new UsuariosService();
        return $service->atualizarUsuario($id, $request);
    }

    /**
     * Inutiliza um cadastro no banco de dados
     */
    public function destroy(string $id)
    {
        return "destroy";
    }
}




//verificar se as funções do usuarios service ainda estao tudo ok... tirei o static.
