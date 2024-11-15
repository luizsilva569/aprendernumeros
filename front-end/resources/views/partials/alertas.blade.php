@if (session('response_com_loop_erros'))
<div id="alerta" class="alert alert-danger">
    {{ session('response_com_loop_erros.mensagem') ?? 'Erro desconhecido.' }}
    @if (!empty(session('response_com_loop_erros.errors')))
    <ul>
        @foreach (session('response_com_loop_erros.errors') as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif
</div>
@endif


<!-- Exibição de mensagens de sucesso -->
@if (session('response') && session('response')->body)
<div id="alerta" class="alert alert-success">
    {{ session('response')->body->mensagem }}
</div>
@endif


<!-- Exibição de mensagens de sucesso simples -->
@if (session('mensagem_sucesso'))
<div id="alerta" class="alert alert-success">
    {{ session('mensagem_sucesso') }}
</div>
@endif
