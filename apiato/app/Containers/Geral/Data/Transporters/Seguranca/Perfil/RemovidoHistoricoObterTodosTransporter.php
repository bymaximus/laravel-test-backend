<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Perfil;

use App\Ship\Parents\Transporters\Transporter;

class RemovidoHistoricoObterTodosTransporter extends Transporter
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
