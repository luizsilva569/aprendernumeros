<div id="corpo">

    <!-- Exibição de mensagens de erro -->
    @if (isset($response) && $response)
    @if ($response->status > 299)
    <div id="alerta" class="alert alert-danger">
        {{ $response->mensagem ?? 'Erro desconhecido.' }}
        @if (!empty($response->errors))
        <ul>
            @foreach ($response->errors as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif
    </div>
    @endif
    @else
    <div id="alerta" class="alert alert-warning">
        Nenhuma resposta disponível no momento.
    </div>
    @endif

    <!-- Exibição de mensagens de sucesso -->
    @if (session('response') && session('response')->body)
    <div id="alerta" class="alert alert-success">
        {{ session('response')->body->mensagem }}
    </div>
    @endif

    <!-- Modal para respostas -->
    @include('partialsQuestoes.modalResposta')

    <!-- Modal para ranking -->
    @include('partialsQuestoes.modalRanking')


    <div id="quadroQuestao">
        <p id="textoQuadroQuestao">{{ $response->objeto['questao_texto'] ?? '' }}</p>
    </div>

    <div id="resposta">
        <!-- Campo para a resposta do usuário -->
        <input type="number" name="resposta" class="form-control" autocomplete="off" id="formResposta" placeholder="Resposta" aria-label="default input example">

        <!-- Botão de envio -->

        <div id="pergunta-container"></div>
        <!-- Campo oculto para o token da questão -->
        <input type="hidden" name="token" value="{{ session('questao_token') }}">
    </div>


    <div class="botoesFormulario">
        <button id="responderBtn" class="btn btn-primary m-5">Responder</button>
    </div>


    <div class="botoesFormulario">
    <button id="ranking-btn" class="btn btn-secondary m-5">Ranking</button>
    <button id="nova-pergunta-btn" class="btn btn-secondary m-5">Nova Pergunta</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Função para o botão "Nova Pergunta"


        $('#nova-pergunta-btn').click(function(event) {
            event.preventDefault(); // Evita o comportamento padrão de recarregar a página
            $('#alerta').hide();
            $('body').css('cursor', 'wait');

            $.ajax({
                url: '/questoes/nova',
                method: 'GET',
                success: function(data) {
                    if (data.objeto) {

                        $('#formResposta').val("");

                        $('#textoQuadroQuestao').text(data.objeto.questao_texto);
                        $('#alerta').removeClass('alert-danger').addClass('alert-success').text('Pergunta carregada com sucesso!').show();
                    } else {
                        $('#alerta').removeClass('alert-success').addClass('alert-danger').text(data.mensagem || 'Objeto não encontrado na resposta.').show();
                        // Exibe os erros retornados
                        if (data.errors) {
                            var errosHtml = '<ul>';
                            $.each(data.errors, function(index, error) {
                                errosHtml += '<li>' + error + '</li>'; // Adiciona cada erro em uma lista
                            });
                            errosHtml += '</ul>';

                            $('#alerta').append(errosHtml); // Adiciona a lista de erros ao alerta
                        }
                        console.error(data.mensagem, data.errors);
                    }
                },
                error: function(xhr) {
                    $('#alerta').removeClass('alert-success').addClass('alert-danger').text('Erro ao buscar nova pergunta.').show();
                    console.error('Erro ao buscar nova pergunta:', xhr.responseText);
                },
                complete: function() {
                    $('body').css('cursor', 'default');
                }
            });
        });



        // Evento que escuta quando o modal é fechado
        $('#ResultadoModal').on('hidden.bs.modal', function() {
            // Redireciona para /questoes
            window.location.href = '/questoes';
        });

        // Função para o envio da resposta
        $('#responderBtn').click(function(event) {
            event.preventDefault(); // Evita o comportamento padrão de recarregar a página

            const resposta = $('#formResposta').val();
            const token = $('input[name="token"]').val();

            $.ajax({
                url: '{{ route("questoes.responder") }}',
                method: 'POST',
                data: {
                    resposta: resposta,
                    token: token,
                    _token: '{{ csrf_token() }}' // Adiciona o token CSRF
                },
                success: function(data) {
                    console.log("Dados recebidos da API: " + data);
                    // Verifique se a resposta da API contém o objeto esperado
                    if (data && data.objeto && data.sucesso === true) {
                        // Acesse as propriedades do objeto
                        if (data.objeto['acertou'] === true) {
                            $('#ResultadoModalTitulo').html('<p>Parabéns, você acertou!</p>');
                        } else {
                            $('#ResultadoModalTitulo').html('<p>Que pena, resposta incorreta</p>');
                        }

                        // Faz um forEach no objeto para exibir linha a linha
                        let objetoHtml = '<ul>';
                        $.each(data.objeto, function(chave, valor) {

                            if (chave === "acertou") {
                                return; // Pula esta iteração
                            }

                            objetoHtml += '<li><strong>' + chave + ':</strong> ' + valor + '</li>';
                        });
                        objetoHtml += '</ul>';


                        // Atualiza o modal com o conteúdo formatado
                        $('#ResultadoModalTexto').html(objetoHtml);
                        $('#ResultadoModal').modal('show');
                    } else {
                        // Se não houver o objeto esperado
                        $('#ResultadoModalTitulo').html('<p>Ops!</p>');

                        //faz uma iteração dos erros enviados pela API
                        if (data.errors && data.errors.length > 0) {
                            let errorsHtml = '<ul>'; // Inicia uma lista não ordenada

                            // Itera sobre cada erro e adiciona à lista
                            $.each(data.errors, function(index, error) {
                                errorsHtml += '<li>' + error + '</li>'; // Adiciona o erro à lista
                            });

                            errorsHtml += '</ul>'; // Fecha a lista

                            // Atribui a mensagem ou a lista de erros ao elemento
                            $('#ResultadoModalTexto').html('<p>Erro: resposta inesperada da API.</p>' + errorsHtml);
                        } else {
                            // Se não houver erros, mostra a mensagem padrão
                            $('#ResultadoModalTexto').html(data.mensagem || '<p>Erro: resposta inesperada da API.</p>');
                        }

                        $('#ResultadoModal').modal('show');
                    }
                },
                error: function(xhr) {
                    $('#ResultadoModalTexto').html('<p>Erro ao enviar resposta. Por favor, tente novamente.</p>');
                    $('#ResultadoModal').modal('show');
                    console.error('Erro ao enviar resposta:', xhr.responseText);
                }
            });
        });

        // Função para o ranking
        $('#ranking-btn').click(function(event) {
            event.preventDefault(); // Evita o comportamento padrão de recarregar a página

            $.ajax({
                url: '{{ route("ranking") }}', // A rota para o seu controlador
                method: 'GET',

                success: function(data) {
                    // Verifique se a resposta da API contém o objeto esperado
                    if (data && data.objeto && data.sucesso === true) {
                        let objetoHtml = '<ul>';

                        // Adiciona a sua pontuação e posição ao HTML
                        objetoHtml += '<li><strong>Sua Pontuação:</strong> ' + data.objeto.sua_pontuacao + '</li>';
                        objetoHtml += '<li><strong>Sua Posição:</strong> ' + data.objeto.sua_posicao + '</li>';

                        // Itera sobre o array de ranking
                        $.each(data.objeto.ranking, function(index, item) {
                            objetoHtml += '<li><strong>Nome:</strong> ' + item.nome + ', <strong>Pontuação:</strong> ' + item.pontuacao + ', <strong>Posição:</strong> ' + item.posicao + '</li>';
                        });

                        objetoHtml += '</ul>';

                        // Atualiza o modal com o conteúdo formatado
                        $('#RankingTexto').html(objetoHtml);
                        $('#RankingModal').modal('show');
                    } else {
                        // Se não houver o objeto esperado
                        $('#RankingTexto').html('<p>Ops, ocorreu um erro!</p>');

                        // Faz uma iteração dos erros enviados pela API
                        if (data.errors && data.errors.length > 0) {
                            let errorsHtml = '<ul>'; // Inicia uma lista não ordenada

                            // Itera sobre cada erro e adiciona à lista
                            $.each(data.errors, function(index, error) {
                                errorsHtml += '<li>' + error + '</li>'; // Adiciona o erro à lista
                            });

                            errorsHtml += '</ul>'; // Fecha a lista

                            // Atribui a mensagem ou a lista de erros ao elemento
                            $('#RankingTexto').html('<p>Erro: resposta inesperada da API.</p>' + errorsHtml);
                        } else {
                            // Se não houver erros, mostra a mensagem padrão
                            $('#RankingTexto').html(data.mensagem || '<p>Erro: resposta inesperada da API.</p>');
                        }

                        $('#RankingModal').modal('show');
                    }
                },
                error: function(xhr) {
                    $('#RankingTexto').html('<p>Erro ao solicitar o ranking. Por favor, tente novamente.</p>');
                    $('#RankingModal').modal('show');
                }
            });
        });
    });
</script>

</div>
