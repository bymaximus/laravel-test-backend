<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\AuditModelRepository;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task;
use Exception;

class HistoricoObterRegistroTask extends Task
{
	protected $repository;

	public function __construct(AuditModelRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run($id)
	{
		try {
			/**
			 * @var \App\Containers\Geral\Models\AuditModel
			 */
			return $this->repository->find($id);
		} catch (Exception $err) {
			throw new NotFoundException('Registro não encontrado.');
		}
	}
}
