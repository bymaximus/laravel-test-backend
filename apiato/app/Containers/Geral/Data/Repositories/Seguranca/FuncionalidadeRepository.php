<?php

namespace App\Containers\Geral\Data\Repositories\Seguranca;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class FuncionalidadeRepository
 */
class FuncionalidadeRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
