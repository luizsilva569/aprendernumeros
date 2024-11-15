<?php

namespace App\Http\Controllers;

use App\Http\Api_core\Erros;
use App\Http\Api_core\FuncoesGlobais;
use App\Http\Api_core\GeradorQuestoesMatematicas;
use App\Http\Api_core\Response;
use App\Http\Services\QuestoesRespondidasService;
use App\Http\Services\QuestoesService;
use App\Http\Services\RankingService;
use Illuminate\Http\Request;


class QuestoesRespondidasController extends Controller
{

    public function getQuestao(Request $request)
    {
        return "ok";
        //return  QuestoesRespondidasService::pesquisarQuestaoPorID($request);
    }

    public function index(Request $request)
    {
        return  QuestoesRespondidasService::pesquisarQuestoesRespondidas($request);
    }

    public function store(Request $request)
    {
        return QuestoesRespondidasService::novaQuestaoRespondida($request);
    }

    public function show($id)
    {
        return QuestoesRespondidasService::pesquisarQuestaoPorID($id);
    }

    public function getPontuacao(Request $request)
    {
        return QuestoesRespondidasService::getPontuacaoPorUsuario($request->usuario_logado);
    }


}
