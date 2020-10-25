<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Seguranca;

use App\Containers\Geral\Models\Funcionalidade;
use App\Ship\Parents\Transformers\Transformer;
use DateTime;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class AdicionarPerfilFuncionalidadeTransformer extends Transformer
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
			$resource = new Collection([], new AdicionarPerfilFuncionalidadeTransformer);
		} else {
			$resource = new Collection($entity->subFuncionalidadesTree, new AdicionarPerfilFuncionalidadeTransformer);
		}
		return [
			'codigo' => $entity->id,
			'nome' => $entity->nome,
			'icon' => ($entity->icone ? $entity->icone : (!$entity->url && $entity->subFuncionalidadesTree && count($entity->subFuncionalidadesTree) > 0 ? 'fa fa-folder' : 'fa fa-desktop')),
			'selected' => true,
			'children' => current($manager->createData($resource)->toArray())
		];
	}
}
