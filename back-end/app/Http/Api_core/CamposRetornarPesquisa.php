<?php
namespace App\Http\Api_core;

class CamposRetornarPesquisa
{
    // São os campos que serão pesquisados na query do banco de dados de pesquisas.
    // Define o tipo de dados que será retornado ao usuário.
    const USUARIO_POR_ID = [
        'campos' => "id, nome, email, dtNascimento",
        'tabela' => "usuarios"
    ];


}

?>
