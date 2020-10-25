<?php

namespace App\Containers\Geral\Tasks\Cadastro\Cidade;

use App\Containers\Geral\Data\Repositories\Cadastro\CidadeRepository;
use App\Ship\Parents\Tasks\Task;

class ObterListaTask extends Task
{
    protected $repository;

    public function __construct(CidadeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->select('id', 'nome')->oldest('nome')->get();
    }
}
