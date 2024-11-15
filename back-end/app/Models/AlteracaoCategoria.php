<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlteracaoCategoria extends Model
{
    use HasFactory;

    // Define o nome da tabela associada ao modelo
    protected $table = 'alteracao_categorias';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'categoria_id',
        'campo',
        'valor_anterior',
        'valor_novo',
        'usuario_alterou', // Se estiver presente na tabela
    ];

      //configurações da chave primaria
      protected $primaryKey = 'id';
      public $incrementing = true;
      protected $keyType = 'int';

    // Define os campos que são tratados como timestamps
    public $timestamps = true;

    // Define os relacionamentos com outras models (se necessário)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_alterou');
    }
}
