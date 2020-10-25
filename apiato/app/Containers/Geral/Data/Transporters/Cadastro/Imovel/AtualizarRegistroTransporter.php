<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Imovel;

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
            'id_estado',
            'id_cidade',
            'email',
            'bairro',
            'rua',
            'numero',
            'complemento',
        ],
        'required' => [
            'id',
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
