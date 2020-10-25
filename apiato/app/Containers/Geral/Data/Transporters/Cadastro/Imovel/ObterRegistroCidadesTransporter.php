<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Imovel;

use App\Ship\Parents\Transporters\Transporter;

class ObterRegistroCidadesTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id',
            'id_imovel',
        ],
        'required' => [
            'id',
            'id_imovel',
        ],
        'default' => [
            // provide default values for specific properties here
        ]
    ];
}
