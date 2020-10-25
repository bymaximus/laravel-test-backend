<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Cidade;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'nome',
            'id_estado',
        ],
        'required' => [
            'nome',
            'id_estado',
        ],
        'default' => [
        ]
    ];
}
