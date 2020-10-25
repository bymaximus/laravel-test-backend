<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilFuncionalidadeRepository;
use App\Containers\Geral\Models\Perfil;
use App\Containers\Geral\Models\PerfilFuncionalidade;
use App\Containers\Geral\Models\Funcionalidade;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AdicionarFuncionalidadesTask extends Task
{
	protected $repository;

	public function __construct(PerfilFuncionalidadeRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run(Perfil $perfil, $funcionalidades, $funcionalidadesSelecionadas)
	{
		if (!$perfil ||
			!$perfil->id
		) {
			throw new CreateResourceFailedException('Registro não criado.');
		}
		if (!$funcionalidades) {
			throw new CreateResourceFailedException('Funcionalidades não encontradas.');
		}
		if ($funcionalidadesSelecionadas &&
			is_array($funcionalidadesSelecionadas) &&
			isset($funcionalidadesSelecionadas['funcionalidades']) &&
			$funcionalidadesSelecionadas['funcionalidades'] &&
			is_array($funcionalidadesSelecionadas['funcionalidades'])
		) {
			$funcionalidadesSelecionadas = $funcionalidadesSelecionadas['funcionalidades'];
		}

		if (is_array($funcionalidades) ||
			$funcionalidades instanceof \Illuminate\Database\Eloquent\Collection
		) {
			Funcionalidade::flushQueryCache();
			PerfilFuncionalidade::disableAuditing();
			/**
			 * @var Funcionalidade
			 */
			foreach ($funcionalidades as $funcionalidade) {
				try {
					$ativo = PerfilFuncionalidade::ATIVO_SIM;
					if ($funcionalidadesSelecionadas &&
						is_array($funcionalidadesSelecionadas) &&
						isset($funcionalidadesSelecionadas[$funcionalidade->id]) &&
						$funcionalidadesSelecionadas[$funcionalidade->id] &&
						is_array($funcionalidadesSelecionadas[$funcionalidade->id]) &&
						isset($funcionalidadesSelecionadas[$funcionalidade->id]['ativo']) &&
						isset($funcionalidadesSelecionadas[$funcionalidade->id]['sub'])
					) {
						if (!$funcionalidadesSelecionadas[$funcionalidade->id]['ativo']) {
							$ativo = PerfilFuncionalidade::ATIVO_NAO;
						}
					}
					/**
					 * @var PerfilFuncionalidade
					 */
					$model = $this->repository->create([
						'id_perfil' => $perfil->id,
						'id_funcionalidade' => $funcionalidade->id,
						'ordem' => $funcionalidade->ordem,
						'ativo' => $ativo
					]);
					if ($model &&
						$model->isValid()
					) {
						$this->criarSubFuncionalidades($model, $funcionalidade, $funcionalidadesSelecionadas);
					} else {
						$modelErros = $model->getErrors();
						if ($modelErros &&
							$modelErros instanceof \Illuminate\Support\MessageBag
						) {
							$modelErros = $modelErros->getMessages();
						}
						if (!$modelErros) {
							throw new Exception('Funcionalidade do perfil não criada.');
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
								throw new Exception('Os dados da funcionalidade do perfil são inválidos. Erros: ' . join(' ', $erros));
							}
							throw new Exception('Os dados da funcionalidade do perfil são inválidos.');
						}
					}
				} catch (ValidationException $err) {
					PerfilFuncionalidade::enableAuditing();
					throw $err;
				} catch (Exception $err) {
					PerfilFuncionalidade::enableAuditing();
					throw new CreateResourceFailedException($err->getMessage());
				}
			}
			PerfilFuncionalidade::enableAuditing();
		} else {
			PerfilFuncionalidade::enableAuditing();
			throw new CreateResourceFailedException('Funcionalidades inválidas.');
		}
	}

	private function criarSubFuncionalidades(PerfilFuncionalidade $perfilFuncionalidade, Funcionalidade $funcionalidade, $funcionalidadesSelecionadas)
	{
		if (!$funcionalidade ||
			!$perfilFuncionalidade ||
			!$perfilFuncionalidade->id
		) {
			throw new Exception('Funcionalidade do perfil não criada.');
		}
		if ($funcionalidade->url ||
			!$funcionalidade->subFuncionalidadesTree
		) {
			return;
		}
		if (is_array($funcionalidade->subFuncionalidadesTree) ||
			$funcionalidade->subFuncionalidadesTree instanceof \Illuminate\Database\Eloquent\Collection
		) {
			if ($funcionalidadesSelecionadas &&
				is_array($funcionalidadesSelecionadas) &&
				isset($funcionalidadesSelecionadas[$funcionalidade->id]) &&
				$funcionalidadesSelecionadas[$funcionalidade->id] &&
				is_array($funcionalidadesSelecionadas[$funcionalidade->id]) &&
				isset($funcionalidadesSelecionadas[$funcionalidade->id]['sub'])
			) {
				$funcionalidadesSelecionadas = $funcionalidadesSelecionadas[$funcionalidade->id]['sub'];
			}
			/**
			 * @var Funcionalidade
			 */
			foreach ($funcionalidade->subFuncionalidadesTree as $subFuncionalidade) {
				try {
					$ativo = PerfilFuncionalidade::ATIVO_SIM;
					if (!$perfilFuncionalidade->ativado) {
						$ativo = PerfilFuncionalidade::ATIVO_NAO;
					} else {
						if ($funcionalidadesSelecionadas &&
							is_array($funcionalidadesSelecionadas) &&
							isset($funcionalidadesSelecionadas[$subFuncionalidade->id]) &&
							$funcionalidadesSelecionadas[$subFuncionalidade->id] &&
							is_array($funcionalidadesSelecionadas[$subFuncionalidade->id]) &&
							isset($funcionalidadesSelecionadas[$subFuncionalidade->id]['ativo']) &&
							isset($funcionalidadesSelecionadas[$subFuncionalidade->id]['sub'])
						) {
							if (!$funcionalidadesSelecionadas[$subFuncionalidade->id]['ativo']) {
								$ativo = PerfilFuncionalidade::ATIVO_NAO;
							}
						}
					}
					/**
					 * @var PerfilFuncionalidade
					 */
					$model = $this->repository->create([
						'id_perfil' => $perfilFuncionalidade->id_perfil,
						'id_funcionalidade' => $subFuncionalidade->id,
						'id_parente' => $perfilFuncionalidade->id,
						'ordem' => $subFuncionalidade->ordem,
						'ativo' => $ativo
					]);
					if ($model &&
						$model->isValid()
					) {
						$this->criarSubFuncionalidades($model, $subFuncionalidade, $funcionalidadesSelecionadas);
					} else {
						$modelErros = $model->getErrors();
						if ($modelErros &&
							$modelErros instanceof \Illuminate\Support\MessageBag
						) {
							$modelErros = $modelErros->getMessages();
						}
						if (!$modelErros) {
							throw new Exception('SubFuncionalidade do perfil não criada.');
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
								throw new Exception('Os dados da subfuncionalidade do perfil são inválidos. Erros: ' . join(' ', $erros));
							}
							throw new Exception('Os dados da subfuncionalidade do perfil são inválidos.');
						}
					}
				} catch (ValidationException $err) {
					throw $err;
				} catch (Exception $err) {
					throw new CreateResourceFailedException($err->getMessage());
				}
			}
		}
	}
}
