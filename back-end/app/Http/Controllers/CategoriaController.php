<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoriasService;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{

     /**
     * Apresenta uma pesquisa dinamica de categorias.
     *
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CategoriasService::pesquisarCategorias($request);
    }

    /**
     * Cria uma nova categoria no banco de dados
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
           return CategoriasService::novaCategoria($request);
    }

    /**
     * Apresenta uma pesquisa dinamica de categorias.
     *
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return CategoriasService::pesquisarCategorias($request);
    }

    /**
     * Atualiza um registro no bd
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $usuario_logado = $request->usuario_logado;
        return CategoriasService::atualizarCategoria($id, $request, $usuario_logado);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
       return CategoriasService::deleteCategoria($id);
    }
}
