<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoriasService;
use App\Http\Services\MetodosQuestoesService;
use App\Http\Services\MetodosQuestoessService;
use Illuminate\Http\Request;

class MetodosQuestoesController extends Controller
{

     /**
     * Apresenta uma pesquisa dinamica de categorias.
     *
     * @param \App\Models\Metodos $categoria
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return MetodosQuestoesService::pesquisarMetodosQuestoes($request);
    }

    /**
     * Cria uma nova categoria no banco de dados
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
           return MetodosQuestoesService::novoMetodoQuestoes($request);
    }

    /**
     * Apresenta uma pesquisa dinamica de categorias.
     *
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return MetodosQuestoesService::pesquisarMetodosQuestoesPorID($request);
    }

    /**
     * Atualiza um registro no bd
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Categoria $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return MetodosQuestoesService::atualizarMetodoQuestoes($request);
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
