<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\Usuario;
use App\Ship\Parents\Transformers\Transformer;

class UsuarioTransformer extends Transformer
{
	/**
	 * @var  array
	 */
	protected $defaultIncludes = [
	];

	/**
	 * @var  array
	 */
	protected $availableIncludes = [
	];

	/**
	 * @param Usuario $entity
	 * @return array
	 */
	public function transform(Usuario $entity)
	{
		return [
			'usuario' => $entity->usuario,
			'senha' => $entity->passwordUnHash(),
			'id_perfil' => $entity->id_perfil,
		];
	}
}
