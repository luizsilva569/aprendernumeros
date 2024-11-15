<?php

namespace App\Http\Controllers;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\GeradorQuestoesMatematicas;
use App\Http\Api_core\Response;
use App\Http\Services\QuestoesService;
use Illuminate\Http\Request;


class QuestoesController extends Controller
{

    public function getQuestao (Request $request){
        return  QuestoesService::getQuestao($request);
    }

    public function questoesAutomaticas(Request $request)
    {
        try {
            $rules = [
                'minimo_item1' => 'required|integer',
                'maximo_item1' => 'required|integer',
                'minimo_item2' => 'required|integer',
                'maximo_item2' => 'required|integer',
                'operador' => 'required|integer',
                'categoria' => 'required|integer',
            ];
            $dadosValidados =  $request->validate($rules);

            //dados esperados receber nesta requisição
            $min_item1 = $dadosValidados['minimo_item1'];
            $max_item1 = $dadosValidados['maximo_item1'];
            $min_item2 = $dadosValidados['minimo_item2'];
            $max_item2 = $dadosValidados['maximo_item2'];
            $operador = $dadosValidados['operador'];
            $categoria = $dadosValidados['categoria'];

            switch ($operador) {
                case 1:
                    $operador = GeradorQuestoesMatematicas::OPERADOR_SOMA;
                    break;
            }

            switch ($categoria) {
                case 1:
                    $categoria = GeradorQuestoesMatematicas::CATEGORIA_ALFABETIZACAO;
                    break;
            }

            return  QuestoesService::gerarQuestoesAutomaticas($request,   $min_item1,
                                                                                                                        $max_item1,
                                                                                                                        $min_item2,
                                                                                                                        $max_item2,
                                                                                                                        $operador,
                                                                                                                        $categoria);
        } catch (\Illuminate\Validation\ValidationException $e) {
            //lida com os erros de validação... converte em array e trata ele
            $erros = FuncoesGlobais::converte_exceptionValidacao_em_array($e);
            return Response::jsonEnum(Erros::ERRO_VALIDACAO, $erros, null);
        }
    }


    public function index(Request $request)
    {
        return  QuestoesService::pesquisarQuestoes($request);
    }

    public function store(Request $request)
    {
        return QuestoesService::novaQuestao($request);
    }

    public function show($id)
    {
        return QuestoesService::pesquisarQuestaoPorID($id);
    }

    //o id deve ser passado junto a request.. no msm json
    public function update(Request $request)
    {
        return QuestoesService::atualizarQuestao($request);
    }

    public function destroy($id)
    {
        return QuestoesService::deleteQuestao($id);
    }
}
