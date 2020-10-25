<?php

namespace App\Containers\Geral\Actions\Seguranca\Perfil;

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
			'nome',
			'historico_alteracao',
		]);
		$funcionalidadesSelecionadas = $request->sanitizeInput([
			'funcionalidades',
		]);

		$funcionalidades = Apiato::call('Geral@Seguranca\Funcionalidade\ObterTodosTask', [], ['removeRequestCriteria']);

		DB::beginTransaction();
		try {
			$registro = Apiato::call('Geral@Seguranca\Perfil\AdicionarTask', [$data]);
			if ($registro &&
				$registro->id
			) {
				if ($funcionalidades) {
					Apiato::call('Geral@Seguranca\Perfil\AdicionarFuncionalidadesTask', [$registro, $funcionalidades, $funcionalidadesSelecionadas]);
				}
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
