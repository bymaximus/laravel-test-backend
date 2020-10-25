<?php

namespace App\Containers\Geral\Actions\Cadastro\Contrato;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\CreateResourceFailedException;
use Exception;

class AdicionarAction extends Action
{
    public function run(Request $request)
    {
        $data = $request->sanitizeInput([
            'id_imovel',
            'email',
            'nome',
            'tipo_pessoa',
            'documento',
        ]);

        DB::beginTransaction();
        try {
            $registro = Apiato::call('Geral@Cadastro\Contrato\AdicionarTask', [$data]);
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
