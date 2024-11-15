<?php

namespace App\Http\Controllers;

use App\Http\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function getRanking(Request $request)
    {
        return RankingService::getRanking($request);
    }
}

