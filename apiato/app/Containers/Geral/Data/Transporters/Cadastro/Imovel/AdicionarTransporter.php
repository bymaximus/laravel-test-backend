<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Imovel;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id_estado',
            'id_cidade',
            'email',
            'bairro',
            'rua',
            'numero',
            'complemento',
        ],
        'required' => [
            'id_estado',
            'id_cidade',
            'email',
            'bairro',
            'rua',
        ],
        'default' => [
        ]
    ];
}
