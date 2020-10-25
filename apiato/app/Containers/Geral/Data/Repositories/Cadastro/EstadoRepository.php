<?php

namespace App\Containers\Geral\Data\Repositories\Cadastro;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class EstadoRepository
 */
class EstadoRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
