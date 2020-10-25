<?php

namespace App\Containers\Geral\UI\WEB\Requests\Cadastro\Cidade;

use App\Ship\Parents\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

class AtualizarRegistroRequest extends Request
{
	const FUNCIONALIDADE_URL = '/cadastro/cidade';

	/**
	 * The assigned Transporter for this Request
	 *
	 * @var string
	 */
	protected $transporter = \App\Containers\Geral\Data\Transporters\Cadastro\Cidade\AtualizarRegistroTransporter::class;

	/**
	 * Define which Roles and/or Permissions has access to this request.
	 *
	 * @var  array
	 */
	protected $access = [
		'permissions' => '',
		'roles' => '',
	];

	/**
	 * Id's that needs decoding before applying the validation rules.
	 *
	 * @var  array
	 */
	protected $decode = [
		// 'id',
	];

	/**
	 * Defining the URL parameters (e.g, `/user/{id}`) allows applying
	 * validation rules on them and allows accessing them like request data.
	 *
	 * @var  array
	 */
	protected $urlParameters = [
		'id',
	];

	/**
	 * @return  array
	 */
	public function rules()
	{
		$self = $this;
		return [
			'id' => 'required|exists:cidade,id',
			'id_estado' => [
				'required',
				'numeric',
				Rule::exists('estado', 'id'),
			],
			'nome' => [
				'required',
				'string',
				'min:1',
				'max:100',
				Rule::unique('cidade', 'nome')->where(function ($query) use ($self) {
					$query->whereNull('dt_remocao')
						  ->where('id_estado', '=', $self->id_estado)
						  ->where('id', '<>', $self->id);
				}),
			],
		];
	}

	/**
	 * @return  bool
	 */
	public function authorize()
	{
		$controller = Route::current()->controller;
		if (!$controller ||
			!$controller->isLogged() ||
			!$controller->usuario()
		) {
			return false;
		}
		$usuario = $controller->usuario();
		if (!$usuario ||
			!$usuario->funcionalidadePermitida(self::FUNCIONALIDADE_URL)
		) {
			return false;
		}
		return $this->check([
			'hasAccess',
		]);
	}
}
