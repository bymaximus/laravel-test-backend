<?php

namespace App\Containers\Geral\Actions\Seguranca\Usuario;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class HistoricoObterRegistroAction extends Action
{
	public function run(Request $request)
	{
		return Apiato::call('Geral@Seguranca\Usuario\HistoricoObterRegistroTask', [$request->id_historico]);
	}
}
