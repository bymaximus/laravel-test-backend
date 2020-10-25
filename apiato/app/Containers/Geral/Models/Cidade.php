<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Models\CidadeAtivo;
use App\Containers\Geral\Models\Estado;
use Illuminate\Support\Arr;
use Exception;

/**
 * @property integer $id
 * @property integer $id_estado
 * @property integer $id_ibge
 * @property string $nome
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Cidade extends MainModel
{
    use QueryCacheable;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cidade';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_estado',
        'id_ibge',
        'nome',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_estado' => 'ID Estado',
        'id_ibge' => 'ID IBGE',
        'nome' => 'Nome',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        $self = $this;
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'id_estado' => [
                    'required',
                    'numeric',
                    Rule::exists('estado', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'id_ibge' => [
                    'nullable',
                    'numeric',
                ],
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('cidade', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id_estado', '=', $self->id_estado);
                    }),
                ],
            ];
        } else {
            return [
                'id_estado' => [
                    'required',
                    'numeric',
                    Rule::exists('estado', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'id_ibge' => [
                    'nullable',
                    'numeric',
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
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->criarAtivo();
        });

        static::updated(function ($model) {
            try {
                if ($model->isDirty('nome') ||
                    $model->isDirty('id_estado') ||
                    $model->isDirty('id_ibge')
                ) {
                    CidadeAtivo::flushQueryCache();
                    if ($model->ativos->count()) {
                        $model->ativos()->delete();
                        CidadeAtivo::flushQueryCache();
                    }
                    $model->criarAtivo();
                }
            } catch (Exception $err) {
                throw $err;
            }
        });

        static::deleted(function ($model) {
            try {
                CidadeAtivo::flushQueryCache();
                if ($model->ativos->count()) {
                    $model->ativos()->delete();
                    CidadeAtivo::flushQueryCache();
                }
            } catch (Exception $err) {
                throw $err;
            }
        });
    }

    public function estado()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Estado', 'id_estado', 'id')->withTrashed();
    }

    public function ativos()
    {
        return $this->hasMany('App\Containers\Geral\Models\CidadeAtivo', 'id_cidade', 'id');
    }

    public function ativo()
    {
        return $this->hasOne('App\Containers\Geral\Models\CidadeAtivo', 'id_cidade', 'id')->where(function ($query) {
            $query->whereNull('dt_remocao');
        });
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.id_estado')) {
            $data['new_values']['id_estado'] = $this->getAttribute('id_estado') . ($this->estado ? ' (' . $this->estado->nome . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_estado')) {
            $data['old_values']['id_estado'] = $this->getOriginal('id_estado');
            if ($this->getOriginal('id_estado') && ($estadoAntigo = Estado::withTrashed()->find($this->getOriginal('id_estado')))) {
                $data['old_values']['id_estado'] .= ' (' . $estadoAntigo->nome . ')';
            }
        }

        return $data;
    }

    public function criarAtivo()
    {
        try {
            CidadeAtivo::flushQueryCache();
            $modelAtivo = new CidadeAtivo();
            $modelAtivo->fill([
                'id_cidade' => $this->id,
                'id_estado' => $this->id_estado,
                'id_ibge' => $this->id_ibge,
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
