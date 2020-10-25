<?php

namespace App\Containers\Geral\Tasks\Cadastro\Cidade;

use App\Containers\Geral\Data\Repositories\Cadastro\CidadeRepository;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task;
use Exception;

class ObterRegistroTask extends Task
{
    protected $repository;

    public function __construct(CidadeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id, $removido = false)
    {
        try {
            /**
             * @var \App\Containers\Geral\Models\Cidade
             */
            if ($removido) {
                return $this->repository->makeModel()->onlyTrashed()->find($id);
            } else {
                return $this->repository->find($id);
            }
        } catch (Exception $err) {
            throw new NotFoundException('Registro não encontrado.');
        }
    }
}
