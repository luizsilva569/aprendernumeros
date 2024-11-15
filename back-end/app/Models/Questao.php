<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Questao extends Model
{
    use HasFactory;

    // Define a tabela associada no banco de dados
    protected $table = 'questoes';

    // Define os campos que podem ser atribuídos em massa.
    protected $fillable = [
        'token_questao',
        'questao_texto',
        'item1',
        'item2',
        'item3',
        'metodo_id',
        'resposta',
        'pontuacao',
        'categoria_id',
        'ativo'
    ];

    // Define os campos que devem ser ocultados quando a model é convertida em array ou JSON.
    protected $hidden = [
        'created_at',
        'updated_at',
        'resposta',
        'metodo_id',
        'ativo',
        'id',
        'item1',
        'item2',
        'item3',
    ];

    // Define os campos que devem ser convertidos para tipos nativos.
    protected $casts = [
        'ativo' => 'boolean'
    ];

    // Configurações da chave primária
    protected $primaryKey = 'id';
    public $incrementing = false; // Define que o campo de chave primária não é auto-incrementado
    protected $keyType = 'string'; // Define o tipo da chave primária como string

    // Define os timestamps padrão (created_at, updated_at).
    public $timestamps = true;

}
