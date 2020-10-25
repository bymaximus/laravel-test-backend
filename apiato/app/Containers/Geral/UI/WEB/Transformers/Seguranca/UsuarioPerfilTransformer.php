<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\Perfil;
use App\Ship\Parents\Transformers\Transformer;

class UsuarioPerfilTransformer extends Transformer
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
	 * @param Perfil $entity
	 * @return array
	 */
	public function transform(Perfil $entity)
	{
		return $entity->toArray();
	}
}
