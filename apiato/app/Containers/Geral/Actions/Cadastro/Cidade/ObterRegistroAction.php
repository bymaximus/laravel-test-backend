<?php

namespace App\Containers\Geral\Actions\Cadastro\Cidade;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterRegistroAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\Cidade\ObterRegistroTask', [$request->id]);
    }
}
