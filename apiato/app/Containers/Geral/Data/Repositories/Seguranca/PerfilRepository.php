<?php

namespace App\Containers\Geral\Data\Repositories\Seguranca;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class PerfilRepository
 */
class PerfilRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
