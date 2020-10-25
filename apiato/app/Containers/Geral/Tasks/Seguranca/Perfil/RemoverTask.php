<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilRepository;
use App\Containers\Geral\Models\Perfil;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
	protected $repository;

	public function __construct(PerfilRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run($id)
	{
		try {
			Perfil::flushQueryCache();
			/**
			 * @var Perfil
			 */
			$model = $this->repository->find($id);
			if ($model) {
				if ($model->isValid()) {
					if ($model->delete()) {
						return $model;
					} else {
						throw new Exception('Erro ao remover registro.');
					}
				} else {
					$modelErros = $model->getErrors();
					if ($modelErros &&
						$modelErros instanceof \Illuminate\Support\MessageBag
					) {
						$modelErros = $modelErros->getMessages();
					}
					if (!$modelErros) {
						throw new Exception('Os dados fornecidos são inválidos.');
					} else {
						$erros = [];
						foreach ($modelErros as $campo => $campoErros) {
							if (is_array($campoErros)) {
								$erros[] = $campoErros[0];
							} else {
								$erros[] = $campoErros;
							}
						}
						if ($erros) {
							throw new Exception('Os dados fornecidos são inválidos. Erros: ' . join(' ', $erros));
						}
						throw new Exception('Os dados fornecidos são inválidos.');
					}
				}
			} else {
				throw new Exception('Registro não encontrado.');
			}
		} catch (ValidationException $err) {
			throw $err;
		} catch (Exception $err) {
			throw new DeleteResourceFailedException($err->getMessage());
		}
	}
}
