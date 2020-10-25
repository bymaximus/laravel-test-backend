<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Usuario;

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
            'id_perfil',
            'usuario',
            'senha',
        ],
        'required' => [
            'id',
            'id_perfil',
            'usuario',
            'senha',
        ],
        'default' => [
        ]
    ];
}
