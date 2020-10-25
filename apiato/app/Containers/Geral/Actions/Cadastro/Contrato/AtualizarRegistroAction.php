<?php

namespace App\Containers\Geral\Actions\Cadastro\Contrato;

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
            'id_imovel',
            'nome',
            'email',
            'tipo_pessoa',
            'documento',
        ]);
        DB::beginTransaction();
        try {
            $registro = Apiato::call('Geral@Cadastro\Contrato\AtualizarRegistroTask', [$request->id, $data]);
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
