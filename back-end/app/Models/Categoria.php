<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Define a tabela associada no banco de dados
    protected $table = 'categorias';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'categoria',
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    // Define os timestamps padrão (created_at, updated_at)
    public $timestamps = true;

      //configurações da chave primaria
      protected $primaryKey = 'id';
      public $incrementing = true;
      protected $keyType = 'int';
}
