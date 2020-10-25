<?php

namespace App\Containers\Geral\Tasks\Seguranca\Usuario;

use App\Containers\Geral\Data\Repositories\Seguranca\UsuarioRepository;
use App\Containers\Geral\Models\Usuario;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AtualizarRegistroTask extends Task
{
    protected $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id, array $data)
    {
        try {
            /**
             * @var Usuario
             */
            $model = $this->repository->find($id);
            if ($model) {
                $model->fill($data);
                $model->salvaOriginal();
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
