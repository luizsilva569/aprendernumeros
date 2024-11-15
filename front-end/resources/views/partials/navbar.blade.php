<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/home">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/questoes">Questões</a>
                </li>

                @if(session('nome_usuario')) <!-- Verifica se o usuário está autenticado -->
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="/logout">Sair</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                @endif
            </ul>
            <span class="navbar-text">
                Seja bem-vindo(a) {{ session('nome_usuario') ?? 'Visitante' }} <!-- Exibe "Visitante" se não houver nome de usuário -->
            </span>
        </div>
    </div>
</nav>
