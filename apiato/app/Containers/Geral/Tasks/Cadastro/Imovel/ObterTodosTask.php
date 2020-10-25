<?php

namespace App\Containers\Geral\Tasks\Cadastro\Imovel;

use App\Containers\Geral\Data\Repositories\Cadastro\ImovelRepository;
use App\Ship\Parents\Tasks\Task;

class ObterTodosTask extends Task
{
    protected $repository;

    public function __construct(ImovelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->repository->makeModel()->with(['estadoAtivo', 'cidadeAtivo']);
    }
}
