<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Exception;

/**
 * @property integer $id
 * @property integer $id_perfil
 * @property integer $id_funcionalidade
 * @property integer $id_parente
 * @property integer $ordem
 * @property integer $ativo
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class PerfilFuncionalidade extends MainModel
{
    use QueryCacheable;

    const ATIVO_SIM = 1;

    const ATIVO_NAO = 0;

    public $cacheFor = 180;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perfil_funcionalidade';

    protected $attributes = [
        'ativo' => self::ATIVO_SIM,
        'ordem' => 0,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_perfil',
        'id_funcionalidade',
        'id_parente',
        'ordem',
        'ativo',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_perfil' => 'ID Perfil',
        'id_funcionalidade' => 'ID Funcionalidade',
        'id_parente' => 'ID Parente',
        'ordem' => 'Ordem',
        'ativo' => 'Ativo',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        return [
            'id_perfil' => [
                'required',
                'numeric',
                Rule::exists('perfil', 'id'),
            ],
            'id_funcionalidade' => [
                'required',
                'numeric',
                Rule::exists('funcionalidade', 'id'),
            ],
            'id_parente' => [
                'nullable',
                'numeric',
                Rule::exists('perfil_funcionalidade', 'id'),
            ],
            'ordem' => 'required|numeric|digits_between:1,11|min:0',
            'ativo' => [
                'required',
                'numeric',
                Rule::in(array_keys(self::getAtivoLista()))
            ],
        ];
    }


    public function getAtivadoAttribute()
    {
        if ($this->ativo === self::ATIVO_SIM) {
            return true;
        }
        return false;
    }

    public function getAtivoTextoAttribute()
    {
        if ($this->ativo == null) {
            return 'Indefinido';
        }
        $list = self::getAtivoLista();
        return ($list &&
            is_array($list) &&
            isset($list[$this->ativo]) ? $list[$this->ativo] : 'Indefinido'
        );
    }

    public static function getAtivoLista()
    {
        return [
            self::ATIVO_NAO => 'Não',
            self::ATIVO_SIM => 'Sim',
        ];
    }

    public function perfil()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Perfil', 'id_perfil', 'id');
    }

    public function funcionalidade()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Funcionalidade', 'id_funcionalidade', 'id');
    }

    public function subFuncionalidades()
    {
        return $this->hasMany('App\Containers\Geral\Models\PerfilFuncionalidade', 'id_parente', 'id')->where([
            ['id_perfil', '=', $this->id_perfil],
        ])->has('funcionalidade')->orderBy('ordem');
    }

    public function parente()
    {
        return $this->belongsTo('App\Containers\Geral\Models\PerfilFuncionalidade', 'id_parente', 'id')->where([
            ['id_perfil', '=', $this->id_perfil],
        ])->has('funcionalidade');
    }
}
