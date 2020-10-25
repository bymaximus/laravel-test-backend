<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade;

use App\Ship\Parents\Transporters\Transporter;

class AdicionarTransporter extends Transporter
{
	/**
	 * @var array
	 */
	protected $schema = [
		'type' => 'object',
		'properties' => [
			'id_parente',
			'nome',
			'url',
			'icone'
		],
		'required' => [
			'nome'
		],
		'default' => [
			'id_parente' => null,
			'url' => '',
			'icone' => ''
		]
	];
}
