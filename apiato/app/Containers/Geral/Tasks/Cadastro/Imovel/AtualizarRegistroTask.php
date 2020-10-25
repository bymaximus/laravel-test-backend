<?php

namespace App\Containers\Geral\Tasks\Cadastro\Imovel;

use App\Containers\Geral\Data\Repositories\Cadastro\ImovelRepository;
use App\Containers\Geral\Models\Imovel;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AtualizarRegistroTask extends Task
{
    protected $repository;

    public function __construct(ImovelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(Imovel $model, array $data)
    {
        try {
            Imovel::flushQueryCache();
            if ($model) {
                $model->fill($data);
                if ($model->saveOrFail() &&
                    $model->isValid()
                ) {
                    return $model;
                } else {
                    $modelErros = $model->getErrors();
                    if ($modelErros &&
                        $modelErros instanceof \Illuminate\Support\MessageBag
                    ) {
                        $modelErros = $modelErros->getMessages();
                    }
                    if (!$modelErros) {
                        throw new Exception('Os dados fornecidos são inválidos.');
                    } else {
                        $erros = [];
                        foreach ($modelErros as $campo => $campoErros) {
                            if (is_array($campoErros)) {
                                $erros[] = $campoErros[0];
                            } else {
                                $erros[] = $campoErros;
                            }
                        }
                        if ($erros) {
                            throw new Exception('Os dados fornecidos são inválidos. Erros: ' . join(' ', $erros));
                        }
                        throw new Exception('Os dados fornecidos são inválidos.');
                    }
                }
            } else {
                throw new Exception('Registro não encontrado.');
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new UpdateResourceFailedException($err->getMessage());
        }
    }
}
