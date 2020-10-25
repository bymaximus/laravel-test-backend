<?php

namespace App\Containers\Geral\Data\Transporters\Cadastro\Estado;

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
			'nome',
			'sigla',
		],
		'required' => [
			'id',
			'nome',
			'sigla',
		],
		'default' => [
		]
	];
}
