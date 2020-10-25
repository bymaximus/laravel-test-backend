<?php

namespace App\Containers\Geral\Tasks\Seguranca\Funcionalidade;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilFuncionalidadeRepository;
use App\Containers\Geral\Models\Perfil;
use App\Containers\Geral\Models\PerfilFuncionalidade;
use App\Containers\Geral\Models\Funcionalidade;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AdicionarPerfilFuncionalidadesTask extends Task
{
	protected $repository;

	public function __construct(PerfilFuncionalidadeRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run(Funcionalidade $funcionalidade)
	{
		if (!$funcionalidade ||
			!$funcionalidade->id
		) {
			throw new CreateResourceFailedException('Registro não criado.');
		}

		if ($funcionalidade->id_parente) {
			if (!$funcionalidade->parente) {
				throw new CreateResourceFailedException('Registro não criado.');
			} elseif (!$funcionalidade->parente->url) {
				$perfilFuncionalidades = PerfilFuncionalidade::where('id_funcionalidade', '=', $funcionalidade->id_parente)->get();
				if ($perfilFuncionalidades &&
					(
						is_array($perfilFuncionalidades) ||
						$perfilFuncionalidades instanceof \Illuminate\Database\Eloquent\Collection
					)
				) {
					PerfilFuncionalidade::flushQueryCache();
					PerfilFuncionalidade::disableAuditing();
					/**
					 * @var PerfilFuncionalidade
					 */
					foreach ($perfilFuncionalidades as $perfilFuncionalidade) {
						try {
							if (!$perfilFuncionalidade ||
								!$perfilFuncionalidade->id ||
								!$perfilFuncionalidade->id_perfil
							) {
								continue;
							}
							$ordem = PerfilFuncionalidade::where('id_parente', $perfilFuncionalidade->id)
									->where('id_perfil', $perfilFuncionalidade->id_perfil)
									->max('ordem');
							if ($ordem) {
								$ordem += 1;
							} else {
								$ordem = 1;
							}
							/**
							 * @var PerfilFuncionalidade
							 */
							$model = $this->repository->create([
								'id_perfil' => $perfilFuncionalidade->id_perfil,
								'id_funcionalidade' => $funcionalidade->id,
								'id_parente' => $perfilFuncionalidade->id,
								'ordem' => $ordem,
								'ativo' => ($perfilFuncionalidade->ativado ? PerfilFuncionalidade::ATIVO_SIM : PerfilFuncionalidade::ATIVO_NAO)
							]);
							if (!$model ||
								!$model->isValid()
							) {
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
				}
			}
		} else {
			Perfil::flushQueryCache();
			$perfis = Perfil::all();
			if ($perfis) {
				PerfilFuncionalidade::flushQueryCache();
				PerfilFuncionalidade::disableAuditing();
				/**
				 * @var Perfil
				 */
				foreach ($perfis as $perfil) {
					$ordem = PerfilFuncionalidade::where('id_perfil', $perfil->id)->where(function ($query) {
						$query->where('id_parente', 0)->orWhereNull('id_parente');
					})->max('ordem');
					if ($ordem) {
						$ordem += 1;
					} else {
						$ordem = 1;
					}
					try {
						/**
						 * @var PerfilFuncionalidade
						 */
						$model = $this->repository->create([
							'id_perfil' => $perfil->id,
							'id_funcionalidade' => $funcionalidade->id,
							'ordem' => $ordem,
							'ativo' => PerfilFuncionalidade::ATIVO_SIM
						]);
						if (!$model ||
							!$model->isValid()
						) {
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
			}
		}
	}
}
