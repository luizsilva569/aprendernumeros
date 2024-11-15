@extends('layouts.app')

@section('content')
    <!-- Modal para feedback de acerto ou erro -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Resultado da Resposta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session('status'))
                        @php
                            $status = session('status');
                        @endphp
                        @if (isset($status->acertou) && $status->acertou)
                            <p>Parabéns, você acertou!</p>
                            <p>Resposta Correta: {{ $status->resposta_correta }}</p>
                            <p>Pontuação: {{ $status->pontuacao }}</p>
                            <p>Pontuação Total: {{ $status->pontuacao_total }}</p>
                        @else
                            <p>Que pena, você errou.</p>
                            <p>Sua Resposta: {{ $status->resposta_usuario }}</p>
                            <p>Resposta Correta: {{ $status->resposta_correta }}</p>
                            <p>Pontuação Total: {{ $status->pontuacao_total }}</p>
                        @endif
                    @else
                        <p>Erro ao processar a resposta. Tente novamente.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Div oculta para armazenar a mensagem de status -->
    <div id="statusMessage" data-status="{{ session('status') ? json_encode(session('status')) : '' }}" style="display: none;"></div>

    <script>
    $(document).ready(function() {
        // Recupera o status armazenado no atributo data-status
        var statusMessage = $('#statusMessage').data('status');

        if (statusMessage) {
            // Exibe o modal se houver uma mensagem de status
            $('#feedbackModal').modal('show');
        }
    });
    </script>
@endsection
