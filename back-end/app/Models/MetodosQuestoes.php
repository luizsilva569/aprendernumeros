<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodosQuestoes extends Model
{
    use HasFactory;

    // Define a tabela associada no banco de dados
    protected $table = 'metodos_questoes';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'ativo',
        'metodo'
    ];

    // Define o campo de chave primária
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Define os timestamps padrão (created_at, updated_at)
    public $timestamps = true;

    // Define os campos que devem ser tratados como booleanos
    protected $casts = [
        'ativo' => 'boolean',
    ];
}
