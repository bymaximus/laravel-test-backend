<?php

namespace App\Containers\Geral\Actions\Seguranca\Usuario;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use App\Ship\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;

class HistoricoObterTodosAction extends Action
{
    public function run(Request $request)
    {
        $dataTablesRequest = app('datatables.request');
        $registro = Apiato::call('Geral@Seguranca\Usuario\ObterRegistroTask', [$request->id], ['removeRequestCriteria']);
        if (!$registro) {
            throw new NotFoundException('Registro não encontrado.');
        }
        $registros = Apiato::call('Geral@Seguranca\Usuario\HistoricoObterTodosTask', [$registro, $dataTablesRequest], ['addRequestCriteria']);
        if ($dataTablesRequest &&
            $dataTablesRequest->orderableColumns()
        ) {
            return datatables()->eloquent($registros->select(['id', 'event', 'created_at', 'user_type', 'user_id']));
        } else {
            return datatables()->eloquent($registros->select(['id', 'event', 'created_at', 'user_type', 'user_id'])->latest('created_at'));
        }
    }
}
