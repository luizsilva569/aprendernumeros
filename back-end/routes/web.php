<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MetodosQuestoes;
use App\Http\Controllers\MetodosQuestoesController;
use App\Http\Controllers\QuestoesController;
use App\Http\Controllers\QuestoesRespondidasController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Seguranca\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


Route::prefix('api/login')->controller(Login::class)->group(function () {
    Route::post('', 'fazerLogin');
    // Intercepta métodos GET e retorna uma mensagem de erro
    Route::any('api/login', function (Request $request) {
    Log::info('Conteúdo do request:', $request->all());
    Log::info('Método da requisição:', ['method' => $request->method()]);
    return response()->json([
        'status' => 405,
        'mensagem' => 'Método não permitido. Use POST para realizar login.'
    ], 405);
})->where('any', '.*');
});

Route::prefix('api/usuarios')->controller(UsuarioController::class)->group(function () {
    Route::get('/listar', 'index');
    Route::get('/listar/{id}', 'show');
    Route::post('/salvar', 'store');
    Route::put('/atualizar/{id}', 'update');
});

Route::prefix('api/categorias')->controller(CategoriaController::class)->group(function () {
    Route::get('/listar', 'index');
    Route::get('/listar/{id}', 'show');
    Route::post('/salvar', 'store');
    Route::put('/atualizar/{id}', 'update');
    Route::delete('/delete/{id}',  'destroy');
});

Route::prefix('api/metodosquestoes')->controller(MetodosQuestoesController::class)->group(function () {
    Route::get('/listar', 'index');
    Route::get('/listar/{id}', 'show');
    Route::post('/salvar', 'store');
    Route::put('/atualizar/{id}', 'update');
    Route::delete('/delete/{id}',  'destroy');
});


Route::prefix('api/questoes')->controller(QuestoesController::class)->group(function () {
    Route::get('/listar', 'index');
    Route::get('/listar/{id}', 'show');
    Route::post('/salvar', 'store');
    Route::post('/automaticas', 'questoesAutomaticas');
    Route::put('/atualizar/{id}', 'update');
});


Route::prefix('api/numeros')->controller(QuestoesRespondidasController::class)->group(function () {
    Route::post('/novo', 'store'); //salvar resposta
    Route::get('/pontuacao', 'getPontuacao');
    Route::get('/ranking', 'getRanking');
});

Route::prefix('api/numeros')->controller(QuestoesController::class)->group(function () {
    Route::get('/novo', 'getQuestao'); //solicitar nova questao
});

Route::prefix('api/ranking')->controller(RankingController::class)->group(function () {
    Route::get('', 'getRanking');
});
