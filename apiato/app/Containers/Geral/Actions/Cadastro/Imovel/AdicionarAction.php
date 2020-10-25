<?php

namespace App\Containers\Geral\Actions\Cadastro\Imovel;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\CreateResourceFailedException;
use Illuminate\Support\Arr;
use Exception;

class AdicionarAction extends Action
{
    public function run(Request $request)
    {
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
            throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
        }
        $estado = Apiato::call('Geral@Cadastro\Estado\ObterRegistroTask', [$data['id_estado']]);
        if (!$estado ||
            !$estado->id ||
            $estado->trashed()
        ) {
            throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
        }

        if (! $data['id_cidade']) {
            throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
        }
        $cidade = Apiato::call('Geral@Cadastro\Cidade\ObterRegistroTask', [$data['id_cidade']]);
        if (!$cidade ||
            !$cidade->id ||
            $cidade->trashed() ||
            ! $cidade->estado ||
            ! $cidade->estado->id ||
            $cidade->estado->id != $estado->id
        ) {
            throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
        }

        DB::beginTransaction();
        try {
            if (!$estado->ativo ||
                !$estado->ativo->id ||
                $estado->ativo->trashed()
            ) {
                $estado->criarAtivo();
                if (!$estado->ativo ||
                    !$estado->ativo->id ||
                    $estado->ativo->trashed()
                ) {
                    throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
                }
            }
            $data['id_estado_ativo'] = $estado->ativo->id;

            if (!$cidade->ativo ||
                !$cidade->ativo->id ||
                $cidade->ativo->trashed()
            ) {
                $cidade->criarAtivo();
                if (!$cidade->ativo ||
                    !$cidade->ativo->id ||
                    $cidade->ativo->trashed()
                ) {
                    throw new CreateResourceFailedException('Registro não criado, os dados fornecidos são inválidos.');
                }
            }
            $data['id_cidade_ativo'] = $cidade->ativo->id;

            Arr::forget($data, 'id_estado');
            Arr::forget($data, 'id_cidade');

            $registro = Apiato::call('Geral@Cadastro\Imovel\AdicionarTask', [$data]);
            if ($registro &&
                $registro->id
            ) {
                DB::commit();
                return $registro;
            } else {
                throw new CreateResourceFailedException('Registro não criado.');
            }
        } catch (Exception $err) {
            DB::rollBack();
            throw $err;
        }
    }
}
