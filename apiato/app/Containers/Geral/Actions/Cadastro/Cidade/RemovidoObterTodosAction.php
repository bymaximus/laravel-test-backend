<?php

namespace App\Containers\Geral\Actions\Cadastro\Cidade;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class RemovidoObterTodosAction extends Action
{
    public function run(Request $request)
    {
        $registros = Apiato::call('Geral@Cadastro\Cidade\RemovidoObterTodosTask', [], ['addRequestCriteria']);
        $request = app('datatables.request');
        if ($request &&
            $request->orderableColumns()
        ) {
            return datatables()->eloquent($registros->select(['id', 'nome', 'id_estado', 'dt_criacao', 'dt_alteracao', 'dt_remocao']));
        } else {
            return datatables()->eloquent($registros->select(['id', 'nome', 'id_estado', 'dt_criacao', 'dt_alteracao', 'dt_remocao'])->oldest('nome'));
        }
    }
}
