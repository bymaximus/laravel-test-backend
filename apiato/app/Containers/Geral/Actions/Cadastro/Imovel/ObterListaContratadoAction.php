<?php

namespace App\Containers\Geral\Actions\Cadastro\Imovel;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterListaContratadoAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\Imovel\ObterListaContratadoTask');
    }
}
