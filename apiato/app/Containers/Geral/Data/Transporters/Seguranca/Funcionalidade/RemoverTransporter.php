<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade;

use App\Ship\Parents\Transporters\Transporter;

class RemoverTransporter extends Transporter
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
			'id'
		],
		'default' => [
		]
	];
}
