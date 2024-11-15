@extends('layouts.main')

@section('title', 'Gandella Labs')


@section ('css')
<link href="{{ asset('public/css/login.css') }}" rel="stylesheet">
@endsection
@section('content')

<!-- Botões para submeter o formulário e voltar -->

<form id="usuarioForm" method="POST" action="{{ route('usuario.store') }}">
    @csrf

    @include('partials.alertas')

    <div id="formularioUsuario">
        <h4 class="mb-4">Cadastro do usuário</h4>

        <label for="usuario" class="form-label">Nome de Usuário</label>
        <input id="usuario" name="nome" class="form-control" type="text" required>

        <label for="email" class="form-label">E-mail</label>
        <input id="email" name="email" class="form-control" type="email" required>

        <label for="senha" class="form-label">Senha</label>
        <input type="password" id="senha" name="senha" class="form-control" required>

        <label for="senha_confirmation" class="form-label">Confirme a Senha</label>
        <input type="password" id="senha_confirmation" name="senha_confirmation" class="form-control" required>

        <!-- Botões para submeter o formulário e voltar -->
        <div class="d-flex justify-content-between mt-3">
       <button type="submit" href="{{ asset('css/login.css') }}" class="btn btn-primary w-100">Cadastrar</button>
        </div>

    </div>
</form>

<script>
    document.getElementById('usuarioForm').addEventListener('submit', function(event) {
        const emailInput = document.getElementById('email');
        const emailValue = emailInput.value;

        // Verifica se o e-mail é válido
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailValue)) {
            alert('Por favor, insira um e-mail válido.');
            event.preventDefault(); // Impede o envio do formulário
            emailInput.focus(); // Foca no campo de e-mail
        }
    });
</script>

@endsection
