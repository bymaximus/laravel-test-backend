<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use App\Containers\Geral\Models\EstadoAtivo;
use App\Containers\Geral\Models\CidadeAtivo;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Exception;

/**
 * @property integer $id
 * @property integer $id_estado_ativo
 * @property integer $id_cidade_ativo
 * @property string $email
 * @property string $bairro
 * @property string $rua
 * @property string $numero
 * @property string $complemento
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Imovel extends MainModel
{
    use QueryCacheable;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imovel';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_estado_ativo',
        'id_cidade_ativo',
        'email',
        'bairro',
        'rua',
        'numero',
        'complemento',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'id_estado_ativo' => 'ID Estado',
        'id_cidade_ativo' => 'ID Cidade',
        'email' => 'Email',
        'bairro' => 'Bairro',
        'rua' => 'Rua',
        'numero' => 'Número',
        'complemento' => 'Complemento',
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
                'id_estado_ativo' => [
                    'required',
                    'numeric',
                    Rule::exists('estado_ativo', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'id_cidade_ativo' => [
                    'required',
                    'numeric',
                    Rule::exists('cidade_ativo', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
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
        } else {
            return [
                'id_estado_ativo' => [
                    'required',
                    'numeric',
                    Rule::exists('estado_ativo', 'id'),
                ],
                'id_cidade_ativo' => [
                    'required',
                    'numeric',
                    Rule::exists('cidade_ativo', 'id'),
                ],
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
        }
    }

    public function estadoAtivo()
    {
        return $this->belongsTo('App\Containers\Geral\Models\EstadoAtivo', 'id_estado_ativo', 'id')->withTrashed();
    }

    public function cidadeAtivo()
    {
        return $this->belongsTo('App\Containers\Geral\Models\CidadeAtivo', 'id_cidade_ativo', 'id')->withTrashed();
    }

    public function contratos()
    {
        return $this->hasMany('App\Containers\Geral\Models\Contrato', 'id_imovel', 'id')->withTrashed();
    }

    public function contratoAtivo()
    {
        return $this->hasOne('App\Containers\Geral\Models\Contrato', 'id_imovel', 'id');
    }

    public function scopeDisponivel($query)
    {
        //dd(get_class($query));
        //^ "Illuminate\Database\Query\Builder"
        //^ "Illuminate\Database\Eloquent\Builder"
        return $query->doesntHave('contratoAtivo');
        /*return $query->whereHas('contratoAtivo', function ($query) {
        });*/
        //return $query->has('contratoAtivo');
        //return $query->doesntHave('contratoAtivo');
        //return $query->whereDoesntHave('contratoAtivo');
        //select count(*) as aggregate from `imovel` where `id` = 1 and (`doesnt_have` = contratoAtivo and `dt_remocao` is null))"
    }

    public function scopeContratado($query)
    {
        //dd(get_class($query));
        //^ "Illuminate\Database\Query\Builder"
        //^ "Illuminate\Database\Eloquent\Builder"
        return $query->has('contratoAtivo');
        /*return $query->whereHas('contratoAtivo', function ($query) {
        });*/
        //return $query->has('contratoAtivo');
        //return $query->doesntHave('contratoAtivo');
        //return $query->whereDoesntHave('contratoAtivo');
        //select count(*) as aggregate from `imovel` where `id` = 1 and (`doesnt_have` = contratoAtivo and `dt_remocao` is null))"
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.id_estado_ativo')) {
            if (
                (
                    !$this->getAttribute('id_estado_ativo') &&
                    $this->relationLoaded('estadoAtivo')
                ) || (
                    $this->getAttribute('id_estado_ativo') &&
                    !$this->relationLoaded('estadoAtivo')
                ) || (
                    $this->getAttribute('id_estado_ativo') &&
                    $this->relationLoaded('estadoAtivo') &&
                    $this->estadoAtivo &&
                    $this->estadoAtivo->id != $this->getAttribute('id_estado_ativo')
                )
            ) {
                $this->load('estadoAtivo');
            }
            $data['new_values']['id_estado_ativo'] = $this->getAttribute('id_estado_ativo') . ($this->estadoAtivo ? ' (' . $this->estadoAtivo->nome . ' - ' . $this->estadoAtivo->sigla . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_estado_ativo')) {
            $data['old_values']['id_estado_ativo'] = $this->getOriginal('id_estado_ativo');
            if ($this->getOriginal('id_estado_ativo') && ($estadoAtivoAntigo = EstadoAtivo::withTrashed()->find($this->getOriginal('id_estado_ativo')))) {
                $data['old_values']['id_estado_ativo'] .= ' (' . $estadoAtivoAntigo->nome.' - '.$estadoAtivoAntigo->sigla . ')';
            }
        }

        if (Arr::has($data, 'new_values.id_cidade_ativo')) {
            if (
                (
                    !$this->getAttribute('id_cidade_ativo') &&
                    $this->relationLoaded('cidadeAtivo')
                ) || (
                    $this->getAttribute('id_cidade_ativo') &&
                    !$this->relationLoaded('cidadeAtivo')
                ) || (
                    $this->getAttribute('id_cidade_ativo') &&
                    $this->relationLoaded('cidadeAtivo') &&
                    $this->cidadeAtivo &&
                    $this->cidadeAtivo->id != $this->getAttribute('id_cidade_ativo')
                )
            ) {
                $this->load('cidadeAtivo');
            }
            $data['new_values']['id_cidade_ativo'] = $this->getAttribute('id_cidade_ativo') . ($this->cidadeAtivo ? ' (' . $this->cidadeAtivo->nome . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_cidade_ativo')) {
            $data['old_values']['id_cidade_ativo'] = $this->getOriginal('id_cidade_ativo');
            if ($this->getOriginal('id_cidade_ativo') && ($cidadeAtivoAntigo = CidadeAtivo::withTrashed()->find($this->getOriginal('id_cidade_ativo')))) {
                $data['old_values']['id_cidade_ativo'] .= ' (' . $cidadeAtivoAntigo->nome. ')';
            }
        }


        return $data;
    }

    public function getStatusAttribute()
    {
        if ($this->contratoAtivo &&
            ! $this->contratoAtivo->trashed()
        ) {
            return 'Contratado';
        }
        return 'Não Contratado';
    }

    public function getBloqueadoAttribute()
    {
        if ($this->contratoAtivo &&
            ! $this->contratoAtivo->trashed()
        ) {
            return true;
        }
        return false;
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

    public function getEnderecoContratoAttribute()
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
        if ($this->complemento &&
            Helpers::string()->clearString($this->complemento)
        ) {
            $values[] = Helpers::string()->clearString($this->complemento);
        }
        if ($this->bairro &&
            Helpers::string()->clearString($this->bairro)
        ) {
            $values[] = Helpers::string()->clearString($this->bairro);
        }
        if ($values) {
            return implode(', ', $values);
        }
        return null;
    }
}
