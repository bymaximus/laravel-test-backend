<?php

namespace App\Containers\Geral\Data\Repositories\Cadastro;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class CidadeRepository
 */
class CidadeRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
