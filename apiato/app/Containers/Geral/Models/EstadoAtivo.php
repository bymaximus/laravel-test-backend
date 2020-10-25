<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use Exception;

/**
 * @property integer $id
 * @property integer $id_estado
 * @property integer $id_ibge
 * @property string $nome
 * @property string $sigla
 * @property string $dt_criacao
 * @property string $dt_remocao
 */
class EstadoAtivo extends MainModel
{
    use QueryCacheable;

    const UPDATED_AT = null;

    public static $auditingDisabled = true;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estado_ativo';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_estado',
        'id_ibge',
        'nome',
        'sigla',
        'dt_criacao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_estado' => 'ID Estado',
        'id_ibge' => 'ID IBGE',
        'nome' => 'Nome',
        'sigla' => 'Sigla',
        'dt_criacao' => 'Dt. Criação',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public $dates = [
        'dt_criacao',
        'dt_remocao',
    ];

    public function getRules()
    {
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
                    Rule::unique('estado_ativo', 'nome')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'sigla' => [
                    'required',
                    'string',
                    'min:2',
                    'max:2',
                    Rule::unique('estado_ativo', 'sigla')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
            ];
        } else {
            $self = $this;
            return [
                'id_estado' => [
                    'required',
                    'numeric',
                    Rule::exists('estado', 'id'),
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
                    Rule::unique('estado_ativo', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id', '<>', $self->id);
                    }),
                ],
                'sigla' => [
                    'required',
                    'string',
                    'min:2',
                    'max:2',
                    Rule::unique('estado_ativo', 'sigla')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id', '<>', $self->id);
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
    }

    public function estado()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Estado', 'id_estado', 'id')->withTrashed();
    }
}
