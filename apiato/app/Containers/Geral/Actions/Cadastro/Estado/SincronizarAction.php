<?php

namespace App\Containers\Geral\Actions\Cadastro\Estado;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class SincronizarAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\Estado\SincronizarTask');
    }
}
