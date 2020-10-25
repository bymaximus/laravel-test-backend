<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade;

use App\Ship\Parents\Transporters\Transporter;

class AtualizarNomeTransporter extends Transporter
{
	/**
	 * @var array
	 */
	protected $schema = [
		'type' => 'object',
		'properties' => [
			'id',
			'nome'
		],
		'required' => [
			'id',
			'nome'
		],
		'default' => [
			// provide default values for specific properties here
		]
	];
}
