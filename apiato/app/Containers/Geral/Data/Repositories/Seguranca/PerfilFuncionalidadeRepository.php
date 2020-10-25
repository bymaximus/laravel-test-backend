<?php

namespace App\Containers\Geral\Data\Repositories\Seguranca;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class PerfilFuncionalidadeRepository
 */
class PerfilFuncionalidadeRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
