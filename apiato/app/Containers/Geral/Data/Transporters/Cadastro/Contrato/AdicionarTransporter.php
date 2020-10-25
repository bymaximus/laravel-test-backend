<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Contrato;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
    /**
     * @var array
     */
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'id_imovel',
            'email',
            'nome',
            'tipo_pessoa',
            'documento',
        ],
        'required' => [
            'id_imovel',
            'email',
            'nome',
            'tipo_pessoa',
            'documento',
        ],
        'default' => [
        ]
    ];
}
