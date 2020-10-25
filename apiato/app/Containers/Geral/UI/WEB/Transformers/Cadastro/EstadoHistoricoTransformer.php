<?php

namespace App\Containers\Geral\UI\WEB\Transformers\Cadastro;

use App\Containers\Geral\Models\AuditModel;
use App\Ship\Parents\Transformers\Transformer;

class EstadoHistoricoTransformer extends Transformer
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
	 * @param AuditModel $entity
	 * @return array
	 */
	public function transform(AuditModel $entity)
	{
		$valoresAntigos = $entity->old_values;
		$valoresNovos = $entity->new_values;
		if (!$valoresAntigos && $valoresNovos) {
			foreach ($valoresNovos as $key => $value) {
				$valoresAntigos[$key] = null;
			}
		} elseif (!$valoresNovos && $valoresAntigos) {
			foreach ($valoresAntigos as $key => $value) {
				$valoresNovos[$key] = null;
			}
		} elseif ($valoresAntigos && $valoresNovos) {
			foreach ($valoresNovos as $key => $value) {
				if (!array_key_exists($key, $valoresAntigos)) {
					$valoresAntigos[$key] = null;
				}
			}
			foreach ($valoresAntigos as $key => $value) {
				if (!array_key_exists($key, $valoresNovos)) {
					$valoresNovos[$key] = null;
				}
			}
		}
		return [
			'valores_novos' => $valoresNovos,
			'valores_antigos' => $valoresAntigos,
		];
	}
}
