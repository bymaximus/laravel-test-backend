<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade;

use App\Ship\Parents\Transporters\Transporter;

class AtualizarIconeTransporter extends Transporter
{
	/**
	 * @var array
	 */
	protected $schema = [
		'type' => 'object',
		'properties' => [
			'id',
			'icone'
		],
		'required' => [
			'id'
		],
		'default' => [
			'icone' => ''
		]
	];
}
