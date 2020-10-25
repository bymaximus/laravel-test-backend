<?php

namespace App\Containers\Geral\Actions\Seguranca\Usuario;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;
use Illuminate\Support\Facades\DB;
use App\Ship\Exceptions\CreateResourceFailedException;
use Exception;

class AdicionarAction extends Action
{
	public function run(Request $request)
	{
		$data = $request->sanitizeInput([
			'id_perfil',
			'usuario',
			'senha',
		]);
		DB::beginTransaction();
		try {
			$registro = Apiato::call('Geral@Seguranca\Usuario\AdicionarTask', [$data]);
			if ($registro &&
				$registro->id
			) {
				DB::commit();
				return $registro;
			} else {
				throw new CreateResourceFailedException('Registro não criado.');
			}
		} catch (Exception $err) {
			DB::rollBack();
			throw $err;
		}
	}
}
