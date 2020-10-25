<?php

namespace App\Containers\Geral\Tasks\Cadastro\Estado;

use App\Containers\Geral\Data\Repositories\Cadastro\EstadoRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaTask extends Task
{
	protected $repository;

	public function __construct(EstadoRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run()
	{
		return $this->repository->makeModel()->select('id', 'nome')->oldest('nome')->get();
	}
}
