<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilRepository;
use App\Containers\Geral\Models\Perfil;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AdicionarTask extends Task
{
	protected $repository;

	public function __construct(PerfilRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run(array $data)
	{
		try {
			Perfil::flushQueryCache();
			/**
			 * @var Perfil
			 */
			if ($data['historico_alteracao']) {
				$data['historico_alteracao'] = Perfil::HISTORICO_ALTERACAO_SIM;
			} else {
				$data['historico_alteracao'] = Perfil::HISTORICO_ALTERACAO_NAO;
			}
			$model = $this->repository->create($data);
			if ($model) {
				if ($model->isValid()) {
					return $model;
				} else {
					return $model->saveOrFail();
				}
			} else {
				$modelErros = $model->getErrors();
				if ($modelErros &&
					$modelErros instanceof \Illuminate\Support\MessageBag
				) {
					$modelErros = $modelErros->getMessages();
				}
				if (!$modelErros) {
					throw new Exception('Registro não criado.');
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
				//throw new Exception('Registro não criado.');
			}
			/*$model = $this->repository->find($id);
			if ($model) {
				$model->fill($data);

				if (!$model->parente_id) {
					$model->parente_id = null;
				}

				$model->alterado = $model::ALTERADO_SIM;
				$model->saveOrFail();
				if ($model->isValid()) {
					return $model;
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
			}*/
		} catch (ValidationException $err) {
			throw $err;
		} catch (Exception $err) {
			throw new CreateResourceFailedException($err->getMessage());
		}
	}
}
