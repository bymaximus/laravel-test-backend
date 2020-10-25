<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Exception;

/**
 * @property integer $id
 * @property integer $id_parente
 * @property int $ordem
 * @property string $nome
 * @property string $url
 * @property string $icone
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Funcionalidade extends MainModel
{
    use QueryCacheable;

    public $cacheFor = 180;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funcionalidade';

    protected $attributes = [
        'ordem' => 0,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_parente',
        'ordem',
        'nome',
        'url',
        'icone',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_parente' => 'ID Parente',
        'ordem' => 'Ordem',
        'nome' => 'Nome',
        'url' => 'URL',
        'icone' => 'Ícone',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        return [
            'id_parente' => [
                'nullable',
                'numeric',
                Rule::exists('funcionalidade', 'id'),
            ],
            'ordem' => 'required|numeric|digits_between:1,11|min:0',
            'nome' => 'required|string|min:1|max:100',
            'url' => 'nullable|string|max:255',
            'icone' => 'nullable|string|max:100',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!$model->id_parente ||
                $model->id_parente == -1
            ) {
                $model->id_parente = null;
            }
            if (!$model->exists ||
                !$model->id
            ) {
                $ordem = 0;
                if (!$model->id_parente ||
                    $model->id_parente == -1
                ) {
                    $model->id_parente = null;
                    $ordem = self::where('id_parente', 0)
                            ->orWhereNull('id_parente')
                            ->max('ordem');
                } else {
                    $ordem = self::where('id_parente', $model->id_parente)
                            ->max('ordem');
                }
                if ($ordem) {
                    $ordem += 1;
                } else {
                    $ordem = 1;
                }
                $model->ordem = $ordem;
            }
        });

        self::registerModelEvent('validating', function ($model, $event) {
            if (!$model->id_parente) {
                $model->id_parente = null;
            }
        });
    }

    public function perfisFuncionalidades()
    {
        return $this->hasMany('App\Containers\Geral\Models\PerfilFuncionalidade', 'id_funcionalidade', 'id')->orderBy('id_perfil')->orderBy('ordem');
    }

    public function subFuncionalidades()
    {
        return $this->hasMany('App\Containers\Geral\Models\Funcionalidade', 'id_parente', 'id')->orderBy('ordem')->with('subFuncionalidades');
    }

    public function subFuncionalidadesTree()
    {
        return $this->hasMany('App\Containers\Geral\Models\Funcionalidade', 'id_parente', 'id')->select('id', 'nome', 'icone', 'id_parente', 'url', 'ordem')->orderBy('ordem')->with('subFuncionalidadesTree');
    }

    public function parente()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Funcionalidade', 'id_parente', 'id');
    }
}
