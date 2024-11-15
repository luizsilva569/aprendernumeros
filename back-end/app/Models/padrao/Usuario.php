<?php

namespace App\Models\padrao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    // Define a tabela associada no bd
    protected $table = 'usuarios';

    // Define os campos que podem ser atribuídos em massa.
    protected $fillable = [
        'nome',
        'email',
        'senha'
    ];

    // Define os campos que devem ser ocultados quando a model é convertida em array ou JSON.
    protected $hidden = [
        'senha',
        'ativo',
        "created_at",
        "updated_at",

    ];

    // Define os campos que devem ser convertidos para tipos nativos.
    protected $casts = [
        'ativo' => 'boolean',
        'nivel'=>'integer'  // o nível é o mesmo da categoria_id  das questoes....
    ];

    /*
    //Define o formato de resposta do atributo
    public function getDtNascimentoAttribute($value)
{
    return Carbon::parse($value)->format('d/m/Y');
}
*/
    //configurações da chave primaria
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    //define os  timestamps padrões (created_at, updated_at).
    public $timestamps = true;

    // Define o valor padrão para o campo 'ativo'
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($usuario) {

            if (is_null($usuario->ativo)) {
                $usuario->ativo = true; // Define o valor padrão como 'true'
            }
            if (is_null($usuario->nivel)) {
                $usuario->nivel = 1; // Define o valor padrão como 1
            }
        });
    }
}
