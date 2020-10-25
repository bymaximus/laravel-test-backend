<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilFuncionalidadeRepository;
use App\Containers\Geral\Models\Perfil;
use App\Containers\Geral\Models\PerfilFuncionalidade;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AtualizarRegistroFuncionalidadesTask extends Task
{
	protected $repository;

	public function __construct(PerfilFuncionalidadeRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run(Perfil $perfil, $funcionalidades)
	{
		if (!$perfil ||
			!$perfil->id
		) {
			throw new UpdateResourceFailedException('Registro não encontrado.');
		}

		if ($funcionalidades &&
			is_array($funcionalidades) &&
			isset($funcionalidades['funcionalidades']) &&
			$funcionalidades['funcionalidades'] &&
			is_array($funcionalidades['funcionalidades'])
		) {
			$funcionalidades = $funcionalidades['funcionalidades'];
		}

		PerfilFuncionalidade::flushQueryCache();
		if ($perfil->funcionalidades) {
			/**
			 * @var PerfilFuncionalidade
			 */
			foreach ($perfil->funcionalidades as $perfilFuncionalidade) {
				if (!$perfilFuncionalidade ||
					$perfilFuncionalidade->trashed() ||
					!$perfilFuncionalidade->id_perfil ||
					$perfilFuncionalidade->id_perfil != $perfil->id ||
					!$perfilFuncionalidade->id_funcionalidade
				) {
					continue;
				}
				if (!$funcionalidades ||
					!is_array($funcionalidades)
				) {
					if ($perfilFuncionalidade->ativado) {
						$perfilFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_NAO;
						if (!$perfilFuncionalidade->isValid() ||
							!$perfilFuncionalidade->saveOrFail()
						) {
							throw new UpdateResourceFailedException('Erro ao atualizar funcionalidade do perfil.');
						}
					}
				} else {
					if (isset($funcionalidades[$perfilFuncionalidade->id]) &&
						$funcionalidades[$perfilFuncionalidade->id] &&
						is_array($funcionalidades[$perfilFuncionalidade->id]) &&
						isset($funcionalidades[$perfilFuncionalidade->id]['ativo']) &&
						isset($funcionalidades[$perfilFuncionalidade->id]['sub'])
					) {
						if (($funcionalidades[$perfilFuncionalidade->id]['ativo'] && !$perfilFuncionalidade->ativado) ||
							(!$funcionalidades[$perfilFuncionalidade->id]['ativo'] && $perfilFuncionalidade->ativado)
						) {
							if ($funcionalidades[$perfilFuncionalidade->id]['ativo']) {
								$perfilFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_SIM;
							} else {
								$perfilFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_NAO;
							}
							if (!$perfilFuncionalidade->isValid() ||
								!$perfilFuncionalidade->saveOrFail()
							) {
								throw new UpdateResourceFailedException('Erro ao atualizar funcionalidade do perfil.');
							}
						}
						if ($perfilFuncionalidade->subFuncionalidades) {
							if (!$perfilFuncionalidade->ativado) {
								$this->desativarSubFuncionalidades($perfilFuncionalidade);
							} else {
								$this->processarSubFuncionalidades($perfilFuncionalidade, $funcionalidades[$perfilFuncionalidade->id]['sub']);
							}
						}
					}
				}
			}
		}
	}

	private function desativarSubFuncionalidades(PerfilFuncionalidade $perfilFuncionalidade)
	{
		if (!$perfilFuncionalidade ||
			!$perfilFuncionalidade->id
		) {
			throw new Exception('Funcionalidade do perfil não encontrada.');
		}
		if (!$perfilFuncionalidade->subFuncionalidades) {
			return;
		}
		/**
		 * @var PerfilFuncionalidade
		 */
		foreach ($perfilFuncionalidade->subFuncionalidades as $subFuncionalidade) {
			if ($subFuncionalidade->ativado) {
				$subFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_NAO;
				if (!$subFuncionalidade->isValid() ||
					!$subFuncionalidade->saveOrFail()
				) {
					throw new UpdateResourceFailedException('Erro ao atualizar funcionalidade do perfil.');
				}
			}
			if ($subFuncionalidade->subFuncionalidades) {
				$this->desativarSubFuncionalidades($subFuncionalidade);
			}
		}
	}

	private function processarSubFuncionalidades(PerfilFuncionalidade $perfilFuncionalidade, $funcionalidades)
	{
		if (!$perfilFuncionalidade ||
			!$perfilFuncionalidade->id
		) {
			throw new Exception('Funcionalidade do perfil não encontrada.');
		}
		if (!$perfilFuncionalidade->subFuncionalidades) {
			return;
		}
		/**
		 * @var PerfilFuncionalidade
		 */
		foreach ($perfilFuncionalidade->subFuncionalidades as $subFuncionalidade) {
			if (!$funcionalidades ||
				!is_array($funcionalidades)
			) {
				if ($subFuncionalidade->ativado) {
					$subFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_NAO;
					if (!$subFuncionalidade->isValid() ||
						!$subFuncionalidade->saveOrFail()
					) {
						throw new UpdateResourceFailedException('Erro ao atualizar funcionalidade do perfil.');
					}
				}
			} else {
				if (isset($funcionalidades[$subFuncionalidade->id]) &&
					$funcionalidades[$subFuncionalidade->id] &&
					is_array($funcionalidades[$subFuncionalidade->id]) &&
					isset($funcionalidades[$subFuncionalidade->id]['ativo']) &&
					isset($funcionalidades[$subFuncionalidade->id]['sub'])
				) {
					if (($funcionalidades[$subFuncionalidade->id]['ativo'] && !$subFuncionalidade->ativado) ||
						(!$funcionalidades[$subFuncionalidade->id]['ativo'] && $subFuncionalidade->ativado)
					) {
						if ($funcionalidades[$subFuncionalidade->id]['ativo']) {
							$subFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_SIM;
						} else {
							$subFuncionalidade->ativo = PerfilFuncionalidade::ATIVO_NAO;
						}
						if (!$subFuncionalidade->isValid() ||
							!$subFuncionalidade->saveOrFail()
						) {
							throw new UpdateResourceFailedException('Erro ao atualizar funcionalidade do perfil.');
						}
					}
					if ($subFuncionalidade->subFuncionalidades) {
						if (!$subFuncionalidade->ativado) {
							$this->desativarSubFuncionalidades($subFuncionalidade);
						} else {
							$this->processarSubFuncionalidades($subFuncionalidade, $funcionalidades[$subFuncionalidade->id]['sub']);
						}
					}
				}
			}
		}
	}
}
