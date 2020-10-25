<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\PerfilFuncionalidade;
use App\Ship\Parents\Transformers\Transformer;
use DateTime;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class PerfilFuncionalidadeTransformer extends Transformer
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
	 * @param PerfilFuncionalidade $entity
	 * @return array
	 */
	public function transform(PerfilFuncionalidade $entity)
	{
		if (!$entity->funcionalidade ||
			$entity->funcionalidade->trashed()
		) {
			return [];
		}
		$manager = new Manager();
		$manager->setSerializer(new ArraySerializer());
		if ($entity->funcionalidade->url) {
			$resource = new Collection([], new PerfilFuncionalidadeTransformer);
		} else {
			$resource = new Collection($entity->subFuncionalidades, new PerfilFuncionalidadeTransformer);
		}
		return [
			'codigo' => $entity->id,
			'nome' => $entity->funcionalidade->nome,
			'icon' => ($entity->funcionalidade->icone ? $entity->funcionalidade->icone : (!$entity->funcionalidade->url && $entity->subFuncionalidades && count($entity->subFuncionalidades) > 0 ? 'fa fa-folder' : 'fa fa-desktop')),
			'selected' => $entity->ativado,
			'children' => current($manager->createData($resource)->toArray())
		];
	}
}
