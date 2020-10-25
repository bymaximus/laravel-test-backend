<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\Funcionalidade;
use App\Ship\Parents\Transformers\Transformer;
use DateTime;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class FuncionalidadeTransformer extends Transformer
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
	 * @param Funcionalidade $entity
	 * @return array
	 */
	public function transform(Funcionalidade $entity)
	{
		$manager = new Manager();
		$manager->setSerializer(new ArraySerializer());
		if ($entity->url) {
			$resource = new Collection([], new FuncionalidadeTransformer);
		} else {
			$resource = new Collection($entity->subFuncionalidadesTree, new FuncionalidadeTransformer);
		}
		return [
			'codigo' => $entity->id,
			'nome' => $entity->nome,
			'url' => $entity->url,
			'icon' => ($entity->icone ? $entity->icone : (!$entity->url && $entity->subFuncionalidadesTree && count($entity->subFuncionalidadesTree) > 0 ? 'fa fa-folder' : 'fa fa-desktop')),
			'children' => current($manager->createData($resource)->toArray())
		];
	}
}
