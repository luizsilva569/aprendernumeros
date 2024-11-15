@extends('layouts.main')

@section('title', 'Ranking - Gandella Labs')

@section('content')

<h1>Ranking</h1>

<table>
    <thead>
        <tr>
            <th>Posição</th>
            <th>Nome</th>
            <th>Pontuação</th>
        </tr>
    </thead>
    <tbody>
        @if ($ranking && count($ranking) > 0)
            @foreach ($ranking as $key => $item)
                <tr>
                    <td>{{ intval($key) + 1 }}</td> <!-- Converte $key para um inteiro -->
                    <td>{{ $item['nome'] }}</td> <!-- Substitua 'nome' pela chave correta -->
                    <td>{{ $item['pontuacao'] }}</td> <!-- Substitua 'pontuacao' pela chave correta -->
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">Nenhum dado disponível.</td>
            </tr>
        @endif
    </tbody>
</table>

@endsection
