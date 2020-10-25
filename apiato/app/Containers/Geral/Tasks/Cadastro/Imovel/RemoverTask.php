<?php

namespace App\Containers\Geral\Tasks\Cadastro\Imovel;

use App\Containers\Geral\Data\Repositories\Cadastro\ImovelRepository;
use App\Containers\Geral\Models\Imovel;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
    protected $repository;

    public function __construct(ImovelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id)
    {
        try {
            Imovel::flushQueryCache();
            /**
             * @var Imovel
             */
            $model = $this->repository->find($id);
            if ($model) {
                if ($model->bloqueado) {
                    throw new Exception('Não é possível remover um imóvel com contrato ativo. Por favor, primeiro cancele o contrato.');
                }
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
