<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use App\Containers\Geral\Models\EstadoAtivo;
use Exception;

/**
 * @property integer $id
 * @property integer $id_ibge
 * @property string $nome
 * @property string $sigla
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Estado extends MainModel
{
    use QueryCacheable;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estado';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_ibge',
        'nome',
        'sigla',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_ibge' => 'ID IBGE',
        'nome' => 'Nome',
        'sigla' => 'Sigla',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'id_ibge' => [
                    'nullable',
                    'numeric',
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('estado', 'nome')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'sigla' => [
                    'required',
                    'string',
                    'min:2',
                    'max:2',
                    Rule::unique('estado', 'sigla')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
            ];
        } else {
            $self = $this;
            return [
                'id_ibge' => [
                    'nullable',
                    'numeric',
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('estado', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')->where('id', '<>', $self->id);
                    }),
                ],
                'sigla' => [
                    'required',
                    'string',
                    'min:2',
                    'max:2',
                    Rule::unique('estado', 'sigla')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')->where('id', '<>', $self->id);
                    }),
                ],
            ];
        }
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->sigla) {
                $model->sigla = Helpers::string()::toUpper(Helpers::string()::trim($model->sigla));
            }
        });

        static::creating(function ($model) {
            if ($model->sigla) {
                $model->sigla = Helpers::string()::toUpper(Helpers::string()::trim($model->sigla));
            }
        });

        self::registerModelEvent('validating', function ($model, $event) {
            if ($model->sigla) {
                $model->sigla = Helpers::string()::toUpper(Helpers::string()::trim($model->sigla));
            }
        });

        static::created(function ($model) {
            $model->criarAtivo();
        });

        static::updated(function ($model) {
            try {
                if ($model->isDirty('nome') ||
                    $model->isDirty('sigla') ||
                    $model->isDirty('id_ibge')
                ) {
                    EstadoAtivo::flushQueryCache();
                    if ($model->ativos->count()) {
                        $model->ativos()->delete();
                        EstadoAtivo::flushQueryCache();
                    }
                    $model->criarAtivo();
                }
            } catch (Exception $err) {
                throw $err;
            }
        });

        static::deleted(function ($model) {
            try {
                EstadoAtivo::flushQueryCache();
                if ($model->ativos->count()) {
                    $model->ativos()->delete();
                    EstadoAtivo::flushQueryCache();
                }
            } catch (Exception $err) {
                throw $err;
            }
        });
	}

    public function cidades()
    {
        return $this->hasMany('App\Containers\Geral\Models\Cidade', 'id_estado', 'id');
    }

    public function ativos()
    {
        return $this->hasMany('App\Containers\Geral\Models\EstadoAtivo', 'id_estado', 'id');
    }

    public function ativo()
    {
        return $this->hasOne('App\Containers\Geral\Models\EstadoAtivo', 'id_estado', 'id')->where(function ($query) {
            $query->whereNull('dt_remocao');
        });
    }

    public function criarAtivo()
    {
        try {
            EstadoAtivo::flushQueryCache();
            $modelAtivo = new EstadoAtivo();
            $modelAtivo->fill([
                'id_estado' => $this->id,
                'id_ibge' => $this->id_ibge,
                'sigla' => $this->sigla,
                'nome' => $this->nome,
            ]);
            if (!$modelAtivo->saveOrFail() ||
                !$modelAtivo->isValid()
            ) {
                $modelErros = $modelAtivo->getErrors();
                if ($modelErros &&
                    $modelErros instanceof \Illuminate\Support\MessageBag
                ) {
                    $modelErros = $modelErros->getMessages();
                }
                if (!$modelErros) {
                    throw new Exception('Registro ativo não criado, os dados fornecidos são inválidos.');
                } else {
                    $erros = [];
                    foreach ($modelErros as $campo => $campoErros) {
                        if (is_array($campoErros)) {
                            $erros[] = $campoErros[0];
                        } else {
                            $erros[] = $campoErros;
                        }
                    }
                    if ($erros) {
                        throw new Exception('Registro ativo não criado, os dados fornecidos são inválidos. Erros: ' . join(' ', $erros));
                    }
                    throw new Exception('Registro ativo não criado, os dados fornecidos são inválidos.');
                }
            }
        } catch (Exception $err) {
            throw $err;
        }
    }
}
