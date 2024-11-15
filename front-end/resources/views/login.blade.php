@extends('layouts.mainlogin')

@section('title', 'Gandella labs - Login')

@section('css')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page">
    <form method="POST" class="formLogin" action="{{ route('efetuaLogin') }}" id="loginForm">
        @csrf
        <h1>Login</h1>
        <p>Digite os seus dados de acesso no campo abaixo.</p>

        @include('partials.alertas')

        <label for="email">E-mail</label>
        <input type="email" name="email" placeholder="Digite seu e-mail" autofocus="true" required />

        <label for="password">Senha</label>
        <input type="password" name="senha" placeholder="Digite sua senha" required />

        <a href="/usuario" style="font-size: 1.2em; margin-bottom: 8px;">Cadastrar-se</a>

        <input type="submit" value="Acessar" class="btn" />
    </form>
</div>

@section('scripts')
<script>
    // Função para limpar os alertas
    function clearAlerts() {
        const alertas = document.querySelectorAll('.alert'); // Seleciona todos os elementos com a classe 'alert'
        alertas.forEach(alerta => {
            alerta.remove(); // Remove cada alerta
        });
    }

    // Adiciona eventos de input para limpar os alertas ao digitar nos inputs
    document.querySelectorAll('input[type="email"], input[type="password"]').forEach(input => {
        input.addEventListener('input', clearAlerts); // Remove alertas ao digitar
    });

    document.getElementById('loginForm').onsubmit = function() {
        document.getElementById('loginButton').disabled = true; // Desabilita o botão após o clique
    };
</script>
@endsection
