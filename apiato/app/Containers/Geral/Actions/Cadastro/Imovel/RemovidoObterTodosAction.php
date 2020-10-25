<?php

namespace App\Containers\Geral\Actions\Cadastro\Imovel;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class RemovidoObterTodosAction extends Action
{
    public function run(Request $request)
    {
        $registros = Apiato::call('Geral@Cadastro\Imovel\RemovidoObterTodosTask', [], ['addRequestCriteria']);
        $request = app('datatables.request');
        if ($request &&
            $request->orderableColumns()
        ) {
            return datatables()->eloquent($registros->select(['id', 'id_estado_ativo', 'id_cidade_ativo', 'email', 'bairro', 'rua', 'numero', 'complemento', 'dt_criacao', 'dt_alteracao', 'dt_remocao']));
        } else {
            return datatables()->eloquent($registros->select(['id', 'id_estado_ativo', 'id_cidade_ativo', 'email', 'bairro', 'rua', 'numero', 'complemento', 'dt_criacao', 'dt_alteracao', 'dt_remocao'])->oldest('email'));
        }
    }
}
