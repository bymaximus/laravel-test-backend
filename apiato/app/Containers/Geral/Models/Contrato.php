<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use App\Containers\Geral\Models\Imovel;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Exception;

/**
 * @property integer $id
 * @property integer $id_imovel
 * @property integer $tipo_pessoa
 * @property string $email
 * @property string $nome
 * @property string $documento
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Contrato extends MainModel
{
    use QueryCacheable;

    const PESSOA_FISICA = 1;

    const PESSOA_JURIDICA = 2;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contrato';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_imovel',
        'tipo_pessoa',
        'email',
        'nome',
        'documento',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_imovel' => 'ID Imóvel',
        'tipo_pessoa' => 'Tipo Pessoa',
        'email' => 'Email',
        'nome' => 'Nome',
        'documento' => 'Documento',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        $rules = [];
        if (!$this->id ||
            !$this->exists
        ) {
            $rules = [
                'id_imovel' => [
                    'required',
                    'numeric',
                    /*Rule::exists('imovel', 'id')->where(function ($query) {
                        (new Imovel())->scopeDisponivel($query)->whereNull('dt_remocao');
                        //Imovel::disponivel()->whereNull('dt_remocao');
                    }),*/
                    function ($attribute, $value, $fail) {
                        if (Imovel::disponivel()->where('id', '=', $value)->whereNull('dt_remocao')->count() != 1) {
                            $fail($attribute.' é inválido.');
                        }
                    },
                ],
                'email' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'email',
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                ],
                'tipo_pessoa' => [
                    'required',
                    'numeric',
                    Rule::in(array_keys(self::getTipoPessoaLista()))
                ],
            ];
            if ($this->pessoa_fisica) {
                $rules['documento'] = [
                    'required',
                    'string',
                    'min:11',
                    'max:14',
                    'regex:/^\d{3}\.?\d{3}\.?\d{3}\-?\d{2}$/',
                    function ($attribute, $value, $fail) {
                        if (Helpers::string()->validaCPF($value) !== true) {
                            $fail('O CPF é inválido.');
                        }
                    },
                ];
            } elseif ($this->pessoa_juridica) {
                $rules['documento'] = [
                    'required',
                    'string',
                    'min:14',
                    'max:18',
                    'regex:/^\d{2}\.?\d{3}\.?\d{3}\/?\d{4}\-?\d{2}$/',
                    function ($attribute, $value, $fail) {
                        if (Helpers::string()->validaCNPJ($value) !== true) {
                            $fail('O CNPJ é inválido.');
                        }
                    },
                ];
            }
        } else {
            $self = $this;
            $rules = [
                'id_imovel' => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($self) {
                        if ($self->isDirty($attribute)) {
                            if (Imovel::disponivel()->where('id', '=', $value)->whereNull('dt_remocao')->count() != 1) {
                                $fail($attribute.' é inválido3.');
                            }
                        } else {
                            if (Imovel::where('id', '=', $value)->count() != 1) {
                                $fail($attribute.' é inválido4.');
                            }
                        }
                    },
                ],
                'email' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    'email',
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                ],
                'tipo_pessoa' => [
                    'required',
                    'numeric',
                    Rule::in(array_keys(self::getTipoPessoaLista()))
                ],
            ];
            if ($this->pessoa_fisica) {
                $rules['documento'] = [
                    'required',
                    'string',
                    'min:11',
                    'max:14',
                    'regex:/^\d{3}\.?\d{3}\.?\d{3}\-?\d{2}$/',
                    function ($attribute, $value, $fail) {
                        if (Helpers::string()->validaCPF($value) !== true) {
                            $fail('O CPF é inválido.');
                        }
                    },
                ];
            } elseif ($this->pessoa_juridica) {
                $rules['documento'] = [
                    'required',
                    'string',
                    'min:14',
                    'max:18',
                    'regex:/^\d{2}\.?\d{3}\.?\d{3}\/?\d{4}\-?\d{2}$/',
                    function ($attribute, $value, $fail) {
                        if (Helpers::string()->validaCNPJ($value) !== true) {
                            $fail('O CNPJ é inválido.');
                        }
                    },
                ];
            }
        }
        return $rules;
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->documento) {
                $model->documento = preg_replace("#\D# i", '', Helpers::string()::trim($model->documento));
            }
        });

        static::creating(function ($model) {
            if ($model->documento) {
                $model->documento = preg_replace("#\D# i", '', Helpers::string()::trim($model->documento));
            }
        });

        static::created(function ($model) {
            Imovel::flushQueryCache();
        });

        static::updated(function ($model) {
            if ($model->isDirty('id_imovel')) {
                Imovel::flushQueryCache();
            }
        });


        static::deleted(function ($model) {
            Imovel::flushQueryCache();
        });
    }

    public function imovel()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Imovel', 'id_imovel', 'id')->withTrashed();
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.id_imovel')) {
            if (
                (
                    !$this->getAttribute('id_imovel') &&
                    $this->relationLoaded('imovel')
                ) || (
                    $this->getAttribute('id_imovel') &&
                    !$this->relationLoaded('imovel')
                ) || (
                    $this->getAttribute('id_imovel') &&
                    $this->relationLoaded('imovel') &&
                    $this->imovel &&
                    $this->imovel->id != $this->getAttribute('id_imovel')
                )
            ) {
                $this->load('imovel');
            }
            $data['new_values']['id_imovel'] = $this->getAttribute('id_imovel') . ($this->imovel ? ' (' . $this->imovel->endereco_contrato . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_imovel')) {
            $data['old_values']['id_imovel'] = $this->getOriginal('id_imovel');
            if ($this->getOriginal('id_imovel') && ($imovelAntigo = Imovel::withTrashed()->find($this->getOriginal('id_imovel')))) {
                $data['old_values']['id_imovel'] .= ' (' . $imovelAntigo->endereco_contrato . ')';
            }
        }

        if (Arr::has($data, 'new_values.tipo_pessoa')) {
            $data['new_values']['tipo_pessoa'] = $this->getTipoPessoaTextoAttribute($this->getAttribute('tipo_pessoa'));
        }
        if (Arr::has($data, 'old_values.tipo_pessoa')) {
            $data['old_values']['tipo_pessoa'] = $this->getTipoPessoaTextoAttribute($this->getOriginal('tipo_pessoa'));
        }

        if (Arr::has($data, 'new_values.documento')) {
            $data['new_values']['documento'] = $this->getDocumentoFormatadoAttribute($this->getAttribute('documento'));
        }
        if (Arr::has($data, 'old_values.documento')) {
            $data['old_values']['documento'] = $this->getDocumentoFormatadoAttribute($this->getOriginal('documento'));
        }


        return $data;
    }

    public function getPessoaFisicaAttribute()
    {
        if ($this->tipo_pessoa === self::PESSOA_FISICA) {
            return true;
        }
        return false;
    }

    public function getPessoaJuridicaAttribute()
    {
        if ($this->tipo_pessoa === self::PESSOA_JURIDICA) {
            return true;
        }
        return false;
    }

    public function getTipoPessoaTextoAttribute($value = null)
    {
        if ($value === null) {
            $value = $this->tipo_pessoa;
        }
        if ($value === null) {
            return 'Indefinido';
        }
        $list = self::getTipoPessoaLista();
        return ($list &&
            is_array($list) &&
            isset($list[$value]) ? $list[$value] : 'Indefinido'
        );
    }

    public static function getTipoPessoaLista()
    {
        return [
            self::PESSOA_FISICA => 'Física',
            self::PESSOA_JURIDICA => 'Jurídica',
        ];
    }

    public function getDocumentoFormatadoAttribute($value = null)
    {
        if ($value === null) {
            $value = $this->documento;
        }
        if ($value) {
            if ($this->pessoa_fisica) {
                if (strlen($value) == 11) {
                    return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9);
                }
            } elseif ($this->pessoa_juridica) {
                if (strlen($value) == 14) {
                    return substr($value, 0, 2) . '.' . substr($value, 2, 3) . '.' . substr($value, 5, 3) . '/' . substr($value, 8, 4) . '-' . substr($value, 12, 2);
                }
            }
            return $value;
        }
        return 'Indefinido';
    }


    public function getEnderecoAttribute()
    {
        $values = [];
        if ($this->rua &&
            Helpers::string()->clearString($this->rua)
        ) {
            $values[] = Helpers::string()->clearString($this->rua);
        }
        if ($this->numero &&
            Helpers::string()->clearString($this->numero)
        ) {
            $values[] = Helpers::string()->clearString($this->numero);
        }
        if ($this->id_cidade_ativo &&
            $this->cidadeAtivo &&
            $this->cidadeAtivo->nome &&
            Helpers::string()->clearString($this->cidadeAtivo->nome)
        ) {
            $values[] = Helpers::string()->clearString($this->cidadeAtivo->nome);
        }
        if ($this->id_estado_ativo &&
            $this->estadoAtivo &&
            $this->estadoAtivo->nome &&
            Helpers::string()->clearString($this->estadoAtivo->nome)
        ) {
            $values[] = Helpers::string()->clearString($this->estadoAtivo->nome);
        }
        if ($values) {
            return implode(', ', $values);
        }
        return null;
    }
}
