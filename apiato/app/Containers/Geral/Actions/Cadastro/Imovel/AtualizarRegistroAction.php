<?php

namespace App\Containers\Geral\Actions\Cadastro\Imovel;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\UpdateResourceFailedException;
use Illuminate\Support\Arr;
use Exception;

class AtualizarRegistroAction extends Action
{
    public function run(Request $request)
    {
        $estado = null;
        $cidade = null;
        $registro = Apiato::call('Geral@Cadastro\Imovel\ObterRegistroTask', [$request->id]);
        if (!$registro ||
            !$registro->id
        ) {
            throw new UpdateResourceFailedException('Registro não atualizado.');
        } elseif ($registro->bloqueado) {
            throw new UpdateResourceFailedException('Não é possível alterar um imóvel que tenha um contrato ativo.');
        }

        $data = $request->sanitizeInput([
            'email',
            'id_estado',
            'id_cidade',
            'bairro',
            'rua',
            'numero',
            'complemento',
        ]);

        if (! $data['id_estado']) {
            throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
        }
        if ($data['id_estado'] != -2 &&
            (
                ! $registro->estadoAtivo ||
                ! $registro->estadoAtivo->id_estado ||
                $registro->estadoAtivo->id_estado != $data['id_estado']
            )
        ) {
            $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroTask', [$data['id_estado']]);
            if (!$estado ||
                !$estado->id ||
                $estado->trashed()
            ) {
                throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
            }
        }

        if (! $data['id_cidade']) {
            throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
        }
        if ($data['id_cidade'] != -2 &&
            (
                ! $registro->cidadeAtivo ||
                ! $registro->cidadeAtivo->id_cidade ||
                $registro->cidadeAtivo->id_cidade != $data['id_cidade']
            )
        ) {
            $cidade = Apiato::call('Geral@Cadastro\Cidade\ObterRegistroTask', [$data['id_cidade']]);
            if (!$cidade ||
                !$cidade->id ||
                $cidade->trashed() ||
                ! $cidade->estado ||
                ! $cidade->estado->id ||
                (
                    $estado &&
                    $cidade->estado->id != $estado->id
                ) || (
                    ! $estado &&
                    $registro->estadoAtivo &&
                    $registro->estadoAtivo->id_estado != $cidade->estado->id
                )
            ) {
                throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
            }
        }

        DB::beginTransaction();
        try {
            if ($estado) {
                if (!$estado->ativo ||
                    !$estado->ativo->id ||
                    $estado->ativo->trashed()
                ) {
                    $estado->criarAtivo();
                    if (!$estado->ativo ||
                        !$estado->ativo->id ||
                        $estado->ativo->trashed()
                    ) {
                        throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
                    }
                }
                $data['id_estado_ativo'] = $estado->ativo->id;
            }

            if ($cidade) {
                if (!$cidade->ativo ||
                    !$cidade->ativo->id ||
                    $cidade->ativo->trashed()
                ) {
                    $cidade->criarAtivo();
                    if (!$cidade->ativo ||
                        !$cidade->ativo->id ||
                        $cidade->ativo->trashed()
                    ) {
                        throw new UpdateResourceFailedException('Registro não atualizado, os dados fornecidos são inválidos.');
                    }
                }
                $data['id_cidade_ativo'] = $cidade->ativo->id;
            }

            Arr::forget($data, 'id_estado');
            Arr::forget($data, 'id_cidade');

            $registro = Apiato::call('Geral@Cadastro\Imovel\AtualizarRegistroTask', [$registro, $data]);
            if ($registro &&
                $registro->id
            ) {
                DB::commit();
                return $registro;
            } else {
                throw new UpdateResourceFailedException('Registro não atualizado.');
            }
        } catch (Exception $err) {
            DB::rollBack();
            throw $err;
        }
    }
}
