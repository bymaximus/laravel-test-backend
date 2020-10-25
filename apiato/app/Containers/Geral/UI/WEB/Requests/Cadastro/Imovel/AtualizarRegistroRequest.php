<?php

namespace App\Containers\Geral\UI\WEB\Requests\Cadastro\Imovel;

use App\Ship\Parents\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

class AtualizarRegistroRequest extends Request
{
    const FUNCIONALIDADE_URL = '/cadastro/imovel';

    /**
     * The assigned Transporter for this Request
     *
     * @var string
     */
    protected $transporter = \App\Containers\Geral\Data\Transporters\Cadastro\Imovel\AtualizarRegistroTransporter::class;

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
        $rules = [
            'id' => 'required|exists:imovel,id',
            'email' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'email',
            ],
            'bairro' => 'required|string|min:1|max:100',
            'rua' => 'required|string|min:1|max:100',
            'numero' => 'nullable|string|min:1|max:100',
            'complemento' => 'nullable|string|min:1|max:100',
        ];
        if (! $this->has('id_estado') ||
            $this->input('id_estado') != -2
        ) {
            $rules['id_estado'] = [
                'required',
                'numeric',
                Rule::exists('estado', 'id')->where(function ($query) {
                    $query->whereNull('dt_remocao');
                }),
            ];
        }
        if (! $this->has('id_cidade') ||
            $this->input('id_cidade') != -2
        ) {
            if (! $this->has('id_estado') ||
                $this->input('id_estado') != -2
            ) {
                $self = $this;
                $rules['id_cidade'] = [
                    'required',
                    'numeric',
                    Rule::exists('cidade', 'id')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                            ->where('id_estado', '=', $self->id_estado);
                    }),
                ];
            } else {
                $rules['id_cidade'] = [
                    'required',
                    'numeric',
                    Rule::exists('cidade', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ];
            }
        }
        return $rules;
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