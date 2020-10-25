<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Cidade;

use App\Ship\Parents\Transporters\Transporter;

class AtualizarRegistroTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
            'nome',
            'id_estado',
        ],
        'required' => [
            'id',
            'nome',
            'id_estado',
        ],
        'default' => [
        ]
    ];
}
