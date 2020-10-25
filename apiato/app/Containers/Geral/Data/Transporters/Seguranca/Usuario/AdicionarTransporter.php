<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Usuario;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
	/**
	 * @var array
	 */
	protected $schema = [
		'type' => 'object',
		'properties' => [
			'usuario',
			'senha',
			'id_perfil',
		],
		'required' => [
			'usuario',
			'senha',
			'id_perfil',
		],
		'default' => [
		]
	];
}
