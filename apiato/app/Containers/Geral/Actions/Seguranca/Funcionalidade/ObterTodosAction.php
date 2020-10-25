<?php

namespace App\Containers\Geral\Actions\Seguranca\Funcionalidade;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterTodosAction extends Action
{
	public function run(Request $request)
	{
		return Apiato::call('Geral@Seguranca\Funcionalidade\ObterTodosTask', [], ['addRequestCriteria']);
	}
}
