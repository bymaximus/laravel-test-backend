<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Exception;

/**
 * @property integer $id
 * @property integer $id_cidade
 * @property integer $id_estado
 * @property integer $id_ibge
 * @property string $nome
 * @property string $dt_criacao
 * @property string $dt_remocao
 */
class CidadeAtivo extends MainModel
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
    protected $table = 'cidade_ativo';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_cidade',
        'id_estado',
        'id_ibge',
        'nome',
        'dt_criacao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_cidade' => 'ID Cidade',
        'id_estado' => 'ID Estado',
        'id_ibge' => 'ID IBGE',
        'nome' => 'Nome',
        'dt_criacao' => 'Dt. Criação',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public $dates = [
        'dt_criacao',
        'dt_remocao',
    ];

    public function getRules()
    {
        $self = $this;
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'id_cidade' => [
                    'required',
                    'numeric',
                    Rule::exists('cidade', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
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
                    Rule::unique('cidade_ativo', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id_estado', '=', $self->id_estado);
                    }),
                ],
            ];
        } else {
            return [
                'id_cidade' => [
                    'required',
                    'numeric',
                    Rule::exists('cidade', 'id'),
                ],
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
                    Rule::unique('cidade_ativo', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id_estado', '=', $self->id_estado)
                              ->where('id', '<>', $self->id);
                    }),
                ],
            ];
        }
    }

    public function cidade()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Cidade', 'id_cidade', 'id')->withTrashed();
    }


    public function estado()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Estado', 'id_estado', 'id')->withTrashed();
    }
}
