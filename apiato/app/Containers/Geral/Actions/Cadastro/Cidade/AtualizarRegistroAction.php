<?php

namespace App\Containers\Geral\Actions\Cadastro\Cidade;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\UpdateResourceFailedException;
use Exception;

class AtualizarRegistroAction extends Action
{
    public function run(Request $request)
    {
        $data = $request->sanitizeInput([
            'nome',
            'id_estado',
        ]);
        DB::beginTransaction();
        try {
            $registro = Apiato::call('Geral@Cadastro\Cidade\AtualizarRegistroTask', [$request->id, $data]);
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
