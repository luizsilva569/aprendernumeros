<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlteracaoMetodoQuestoes extends Model
{
    use HasFactory;

    // Define o nome da tabela associada ao model
    protected $table = 'alteracao_metodo_questoes';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'metodo_id',
        'campo',
        'valor_anterior',
        'valor_novo',
        'usuario_alterou',
    ];

    // Define os campos que são tratados como timestamps
    public $timestamps = true;

    // Define a relação com o modelo MetodosQuestoes
    public function metodo()
    {
        return $this->belongsTo(MetodosQuestoes::class, 'metodo_id');
    }

    // Define a relação com o modelo Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_alterou');
    }
}
