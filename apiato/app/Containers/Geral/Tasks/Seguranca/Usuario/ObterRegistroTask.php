<?php

namespace App\Containers\Geral\Tasks\Seguranca\Usuario;

use App\Containers\Geral\Data\Repositories\Seguranca\UsuarioRepository;
use App\Containers\Geral\Models\Usuario;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Tasks\Task;
use Exception;

class ObterRegistroTask extends Task
{
    protected $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id, $removido = false)
    {
        try {
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
