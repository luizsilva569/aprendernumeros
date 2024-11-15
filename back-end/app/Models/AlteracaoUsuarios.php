<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlteracaoUsuarios extends Model
{
    use HasFactory;

    // Define o nome da tabela associada ao model
    protected $table = 'alteracoes_usuarios';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'usuario_id',
        'campo',
        'valor_anterior',
        'valor_novo',
        'usuario_alterou', // Se estiver presente na tabela
    ];

    // Define os campos que são tratados como timestamps
    public $timestamps = true;

      //configurações da chave primaria
      protected $primaryKey = 'id';
      public $incrementing = true;
      protected $keyType = 'int';
}
