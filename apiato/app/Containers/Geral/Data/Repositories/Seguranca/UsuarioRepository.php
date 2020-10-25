<?php

namespace App\Containers\Geral\Data\Repositories\Seguranca;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class UsuarioRepository
 */
class UsuarioRepository extends Repository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];
}
