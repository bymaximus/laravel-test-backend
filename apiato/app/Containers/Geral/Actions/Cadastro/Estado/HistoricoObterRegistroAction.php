<?php

namespace App\Containers\Geral\Actions\Cadastro\Estado;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class HistoricoObterRegistroAction extends Action
{
	public function run(Request $request)
	{
		return Apiato::call('Geral@Cadastro\Estado\HistoricoObterRegistroTask', [$request->id_historico]);
	}
}
