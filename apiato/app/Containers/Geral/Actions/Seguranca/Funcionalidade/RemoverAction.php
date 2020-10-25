<?php

namespace App\Containers\Geral\Actions\Seguranca\Funcionalidade;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\DeleteResourceFailedException;
use Exception;

class RemoverAction extends Action
{
	public function run(Request $request)
	{
		DB::beginTransaction();
		try {
			$registro = Apiato::call('Geral@Seguranca\Funcionalidade\RemoverTask', [$request->id]);
			if ($registro &&
				$registro->id
			) {
				DB::commit();
				return $registro;
			} else {
				throw new DeleteResourceFailedException('Registro não removido.');
			}
		} catch (Exception $err) {
			DB::rollBack();
			throw $err;
		}
	}
}
