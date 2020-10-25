<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaTask extends Task
{
	protected $repository;

	public function __construct(PerfilRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run()
	{
		return $this->repository->makeModel()->select('id', 'nome')->oldest('nome')->get();
	}
}
