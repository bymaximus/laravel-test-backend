<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Perfil;

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
			'historico_alteracao',
			'funcionalidades',
		],
		'required' => [
			'nome',
			'historico_alteracao',
		],
		'default' => [
			'funcionalidades' => []
		]
	];
}
