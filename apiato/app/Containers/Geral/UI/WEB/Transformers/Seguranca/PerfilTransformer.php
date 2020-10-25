<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\Perfil;
use App\Ship\Parents\Transformers\Transformer;

class PerfilTransformer extends Transformer
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
		return [
			'nome' => $entity->nome,
			'historico_alteracao' => $entity->tem_historico_alteracao,
		];
	}
}
