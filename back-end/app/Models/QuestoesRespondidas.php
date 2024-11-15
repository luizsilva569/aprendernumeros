<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestoesRespondidas extends Model
{
    use HasFactory;

    // Definindo o nome da tabela, caso seja diferente do padrão 'questoes_respondidas'
    protected $table = 'questoes_respondidas';

    // Definindo os campos que podem ser preenchidos
    protected $fillable = [
        'questao_id',
        'token_questao',
        'usuario_id',
        'resposta_usuario',
        'resposta_correta',
        'pontuacao',
         'acertou'
    ];

    protected $casts = [
        'acertou' => 'boolean',
    ];

    protected $hidden = [
        "updated_at",
        "created_at",
        'questao_id',
        "id",
        "token_questao",
        "usuario_id",
    ];

    // Definindo os campos que são do tipo date (opcional)
    protected $dates = ['created_at', 'updated_at'];
}

