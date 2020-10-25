<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Estado;

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
			'sigla',
		],
		'required' => [
			'nome',
			'sigla',
		],
		'default' => [
		]
	];
}
