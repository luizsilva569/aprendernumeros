@extends('layouts.mainlogin')

@section('title', 'AprenderNúmeros - Matemática para Crianças')

@section('content')
<style>
   * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: rgb(235, 235, 235);
}

body {
    background: linear-gradient(135deg, #2C3E50, #4A4A4A, #1A1A1A); /* Gradiente escuro com tons de roxo e cinza */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.content {
    flex: 1; /* Faz a div principal ocupar todo o espaço disponível */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.container {
    max-width: 950px;
    text-align: center;
    background: rgba(45, 45, 45, 0.3); /* Fundo do container com leve transparência */
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

h1 {
    font-size: 2.5rem;
    color: #FFD700; /* Amarelo dourado que se destaca */
    margin-bottom: 0.5rem;
    font-weight: bold;
    letter-spacing: 2px; /* Aumenta o espaço entre as letras */
}

h4 {
    font-size: 1.5rem;
    color: #FFDDC1; /* Um tom suave de bege rosado */
    margin-bottom: 1rem;
    font-weight: normal;
}

p {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #E0E0E0; /* Cinza claro para boa legibilidade */
    margin-bottom: 1rem;
    letter-spacing: 0.5px; /* Um pequeno aumento no espaçamento das letras */
    text-align: justify; /* Justifica o texto */
}

.button {
    display: inline-block;
    padding: 12px 40px;
    color: white;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px; /* Bordas levemente arredondadas para um formato retangular */
    font-size: 1.1rem;
    transition: background-color 0.3s, transform 0.3s;
    letter-spacing: 1.5px; /* Aumenta o espaçamento entre as letras */
    margin-top: 20px;
    margin-right: 10px; /* Adiciona um espaço entre os botões */
}

/* Botão "Cadastre-se" */
.button-cadastro {
    background-color: #28a745; /* Verde agradável para o botão "Cadastre-se" */
}

.button-cadastro:hover {
    background-color: #218838; /* Tom de verde mais escuro no hover */
    transform: translateY(-2px); /* Elevação sutil para dar destaque */
}

/* Botão "Entrar" */
.button-entrar {
    background-color: #3498db; /* Azul claro para o botão "Entrar" */
}

.button-entrar:hover {
    background-color: #2980b9; /* Tom de azul mais escuro no hover */
    transform: translateY(-2px); /* Elevação sutil para dar destaque */
}

.navbar {
    width: 100%;
    position: fixed;
    top: 0;

    z-index: 1000;
}




</style>


<div class="content">
    <div class="container">
        <h1>Aprender Números</h1>
<br>
        <h4>Descubra o mundo da matemática de forma divertida e gratuita!</h4>
        <br> 

        <p>Matemática pode ser divertida! O "Aprender Números" foi criado para tornar o aprendizado algo prazeroso e empolgante. Com lições que vão do básico ao intermediário, <strong>AprenderNúmeros</strong> é perfeito para crianças em fase de alfabetização ou que desejam avançar na matemática.</p>

        <p>Aqui, seu filho aprenderá números, somas, subtrações e muito mais, tudo passo a passo.</p>

        <p>A matemática pode ser desafiadora, mas com o apoio certo, toda criança pode se apaixonar por ela!</p>

        <p>Vamos começar essa jornada juntos?</p>

        <!-- Condicional para mostrar a navbar se o usuário estiver logado -->
        @if (session('nome_usuario'))
            <a href="/questoes" class="button button-cadastro">Responder questões!</a>
        @else
            <!-- Caso contrário, mostre os botões de login e cadastro -->
            <div>
                <a href="/usuario" class="button button-cadastro">Cadastre-se gratuitamente!</a>
                <a href="/login" class="button button-entrar">Entrar</a>
            </div>
        @endif
    </div>
</div>

@endsection
