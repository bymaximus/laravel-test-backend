<?php

namespace App\Containers\Geral\Actions\Cadastro\Contrato;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class HistoricoObterRegistroAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\Contrato\HistoricoObterRegistroTask', [$request->id_historico]);
    }
}
