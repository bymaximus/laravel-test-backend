<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Contrato;

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
            'id_imovel',
            'email',
            'nome',
            'tipo_pessoa',
            'documento',
        ],
        'required' => [
            'id',
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
