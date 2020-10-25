<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Usuario;

use App\Ship\Parents\Transporters\Transporter;

class ObterRegistroTransporter extends Transporter
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
        ]
    ];
}
