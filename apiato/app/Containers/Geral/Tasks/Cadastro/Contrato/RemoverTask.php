<?php

namespace App\Containers\Geral\Tasks\Cadastro\Contrato;

use App\Containers\Geral\Data\Repositories\Cadastro\ContratoRepository;
use App\Containers\Geral\Models\Contrato;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
    protected $repository;

    public function __construct(ContratoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id)
    {
        try {
            Contrato::flushQueryCache();
            /**
             * @var Contrato
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
