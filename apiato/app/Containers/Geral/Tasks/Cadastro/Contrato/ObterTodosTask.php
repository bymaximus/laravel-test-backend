<?php

namespace App\Containers\Geral\Tasks\Cadastro\Contrato;

use App\Containers\Geral\Data\Repositories\Cadastro\ContratoRepository;
use App\Ship\Parents\Tasks\Task;

class ObterTodosTask extends Task
{
    protected $repository;

    public function __construct(ContratoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->with('imovel');
    }
}