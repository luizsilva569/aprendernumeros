<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlteracaoQuestoes extends Model
{
    protected $table = 'alteracao_questoes';

    protected $fillable = [
        'questao_id',
        'campo',
        'valor_anterior',
        'valor_novo',
        'usuario_alterou',
    ];

    // Relacionamento com a model Questao
    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }

    // Relacionamento com a model Usuario (ou o que você tiver para usuários)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_alterou');
    }
}
