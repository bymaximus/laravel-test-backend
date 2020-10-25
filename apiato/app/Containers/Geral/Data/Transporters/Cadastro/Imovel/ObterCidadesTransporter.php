<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Imovel;

use App\Ship\Parents\Transporters\Transporter;

class ObterCidadesTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
        ],
        'required' => [
            'id',
        ],
        'default' => [
            // provide default values for specific properties here
        ]
    ];
}
