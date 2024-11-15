<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QuestoesController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/questoes', [QuestoesController::class, 'novaQuestao'])->name('questoes');
Route::get('/questoes/nova', [QuestoesController::class, 'novaQuestaoBotao'])->name('questoes.nova');
Route::post('/questoes/responder', [QuestoesController::class, 'responderQuestao'])->name('questoes.responder');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/efetualogin', [LoginController::class, 'efetuaLogin'])->name('efetuaLogin');
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');

Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index');
Route::post('/usuario', [UsuarioController::class, 'store'])->name('usuario.store');


