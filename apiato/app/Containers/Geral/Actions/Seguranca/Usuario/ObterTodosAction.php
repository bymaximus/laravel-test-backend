<?php

namespace App\Containers\Geral\Actions\Seguranca\Usuario;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterTodosAction extends Action
{
    public function run(Request $request)
    {
        $registros = Apiato::call('Geral@Seguranca\Usuario\ObterTodosTask', [], ['addRequestCriteria']);
        $request = app('datatables.request');
        if ($request &&
            $request->orderableColumns()
        ) {
            return datatables()->eloquent($registros->select(['id', 'id_perfil', 'usuario', 'dt_criacao', 'dt_alteracao', 'dt_acesso']));
        } else {
            return datatables()->eloquent($registros->select(['id', 'id_perfil', 'usuario', 'dt_criacao', 'dt_alteracao', 'dt_acesso'])->oldest('usuario'));
        }
    }
}
