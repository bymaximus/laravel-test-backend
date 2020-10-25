<?php

namespace App\Containers\Geral\Tasks\Seguranca\Perfil;

use App\Containers\Geral\Data\Repositories\Seguranca\PerfilRepository;
use App\Ship\Parents\Tasks\Task;

class ObterTodosTask extends Task
{
	protected $repository;

	public function __construct(PerfilRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run()
	{
		//return $this->repository->makeModel()->where('id_parente', 0)->orWhereNull('id_parente')->select('id', 'nome', 'icone', 'url')->orderBy('ordem')->with('subFuncionalidadesTree')->get();
		return $this->repository->makeModel()->query();
	}
}
