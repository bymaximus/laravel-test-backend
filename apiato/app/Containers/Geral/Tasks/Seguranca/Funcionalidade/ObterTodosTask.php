<?php

namespace App\Containers\Geral\Tasks\Seguranca\Funcionalidade;

use App\Containers\Geral\Data\Repositories\Seguranca\FuncionalidadeRepository;
use App\Containers\Geral\Models\Funcionalidade;
use App\Ship\Parents\Tasks\Task;

class ObterTodosTask extends Task
{
	protected $repository;

	public function __construct(FuncionalidadeRepository $repository)
	{
		$this->repository = $repository;
	}

	public function run()
	{
		Funcionalidade::flushQueryCache();
		return $this->repository->makeModel()->where('id_parente', 0)->orWhereNull('id_parente')->select('id', 'nome', 'icone', 'url', 'ordem')->orderBy('ordem')->with('subFuncionalidadesTree')->get();
	}
}
