<?php

namespace App\Containers\Geral\Tasks\Seguranca\Usuario;

use App\Containers\Geral\Data\Repositories\Seguranca\UsuarioRepository;
use App\Containers\Geral\Models\Usuario;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
    protected $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id)
    {
        try {
            /**
             * @var Usuario
             */
            $model = $this->repository->find($id);
            if ($model) {
                if ($model->delete()) {
                    return $model;
                } else {
                    throw new Exception('Erro ao remover registro.');
                }
            } else {
                throw new Exception('Registro não encontrado.');
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new DeleteResourceFailedException($err->getMessage());
        }
    }
}
