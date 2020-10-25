<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Arr;
use Exception;

/**
 * @property integer $id
 * @property string $nome
 * @property integer $historico_alteracao
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Perfil extends MainModel
{
    use QueryCacheable;

    const HISTORICO_ALTERACAO_SIM = 1;

    const HISTORICO_ALTERACAO_NAO = 0;

    private $menuFuncionalidadesOriginal = null;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perfil';

    protected $primaryKey = 'id';

    protected $attributes = [
        'historico_alteracao' => self::HISTORICO_ALTERACAO_NAO,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'nome',
        'historico_alteracao',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'nome' => 'Nome',
        'historico_alteracao' => 'Histórico de Alteração',
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
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('perfil', 'nome')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'historico_alteracao' => [
                    'required',
                    'numeric',
                    Rule::in(array_keys(self::getHistoricoAlteracaoLista()))
                ],
            ];
        } else {
            $self = $this;
            return [
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('perfil', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')->where('id', '<>', $self->id);
                    }),
                ],
                'historico_alteracao' => [
                    'required',
                    'numeric',
                    Rule::in(array_keys(self::getHistoricoAlteracaoLista()))
                ],
            ];
        }
    }


    public function usuarios()
    {
        return $this->hasMany('App\Containers\Geral\Models\Usuario', 'id_perfil', 'id');
    }

    public function funcionalidades()
    {
        return $this->hasMany('App\Containers\Geral\Models\PerfilFuncionalidade', 'id_perfil', 'id')->where(function ($query) {
            $query->where('id_parente', 0)->orWhereNull('id_parente');
        })->has('funcionalidade')->orderBy('ordem');
    }

    public function getTemHistoricoAlteracaoAttribute()
    {
        if ($this->historico_alteracao === self::HISTORICO_ALTERACAO_SIM) {
            return true;
        }
        return false;
    }

    public function getHistoricoAlteracaoTextoAttribute($value = null)
    {
        if ($this->historico_alteracao === null) {
            return 'Indefinido';
        }
        if ($value === null) {
            $value = $this->historico_alteracao;
        }
        $list = self::getHistoricoAlteracaoLista();
        return ($list &&
            is_array($list) &&
            isset($list[$value]) ? $list[$value] : 'Indefinido'
        );
    }

    public static function getHistoricoAlteracaoLista()
    {
        return [
            self::HISTORICO_ALTERACAO_NAO => 'Não',
            self::HISTORICO_ALTERACAO_SIM => 'Sim',
        ];
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.historico_alteracao')) {
            $data['new_values']['historico_alteracao'] = $this->getHistoricoAlteracaoTextoAttribute($this->getAttribute('historico_alteracao'));
        }
        if (Arr::has($data, 'old_values.historico_alteracao')) {
            $data['old_values']['historico_alteracao'] = $this->getHistoricoAlteracaoTextoAttribute($this->getOriginal('historico_alteracao'));
        }

        return $data;
    }

    public function funcionalidadePermitida($url)
    {
        if (!$this->id ||
            $this->trashed() ||
            !$this->funcionalidades
        ) {
            return false;
        }
        $perfilFuncionalidades = PerfilFuncionalidade::where([
            ['id_perfil', '=', $this->id],
        ])->whereHas('funcionalidade', function (Builder $query) use ($url) {
            $query->where('url', $url);
        })->with('funcionalidade')->get();
        if (!$perfilFuncionalidades ||
            (
                !($perfilFuncionalidades instanceof \Illuminate\Database\Eloquent\Collection) &&
                !is_array($perfilFuncionalidades)
            )
        ) {
            return false;
        }
        $permitido = false;
        foreach ($perfilFuncionalidades as $perfilFuncionalidade) {
            if (!$perfilFuncionalidade ||
                !$perfilFuncionalidade->id ||
                !$perfilFuncionalidade->ativado ||
                $perfilFuncionalidade->trashed() ||
                !$perfilFuncionalidade->funcionalidade ||
                $perfilFuncionalidade->funcionalidade->trashed()
            ) {
                continue;
            }
            if (!$perfilFuncionalidade->id_parente) {
                $permitido = true;
            } elseif (!$perfilFuncionalidade->parente ||
                !$perfilFuncionalidade->parente->id ||
                !$perfilFuncionalidade->parente->ativado ||
                $perfilFuncionalidade->parente->trashed() ||
                !$perfilFuncionalidade->parente->funcionalidade ||
                $perfilFuncionalidade->parente->funcionalidade->trashed()
            ) {
                continue;
            } elseif (!$perfilFuncionalidade->parente->id_parente) {
                $permitido = true;
            } else {
                $perfilFuncionalidade = $perfilFuncionalidade->parente;
                do {
                    if (!$perfilFuncionalidade ||
                        !$perfilFuncionalidade->id ||
                        !$perfilFuncionalidade->ativado ||
                        !$perfilFuncionalidade->funcionalidade ||
                        $perfilFuncionalidade->trashed() ||
                        $perfilFuncionalidade->funcionalidade->trashed()
                    ) {
                        break;
                    } elseif (!$perfilFuncionalidade->id_parente) {
                        $permitido = true;
                        break;
                    } elseif (!$perfilFuncionalidade->parente ||
                        !$perfilFuncionalidade->parente->id ||
                        !$perfilFuncionalidade->parente->ativado ||
                        $perfilFuncionalidade->parente->trashed() ||
                        !$perfilFuncionalidade->parente->funcionalidade ||
                        $perfilFuncionalidade->parente->funcionalidade->trashed()
                    ) {
                        break;
                    } else {
                        $perfilFuncionalidade = $perfilFuncionalidade->parente;
                    }
                } while ($perfilFuncionalidade);
            }
        }
        return $permitido;
    }

    public function menuSubFuncionalidades($perfilFuncionalidade)
    {
        if (!$perfilFuncionalidade ||
            !$perfilFuncionalidade->ativado ||
            $perfilFuncionalidade->trashed() ||
            !$perfilFuncionalidade->id_perfil ||
            $perfilFuncionalidade->id_perfil != $this->id ||
            !$perfilFuncionalidade->id_funcionalidade ||
            !$perfilFuncionalidade->perfil ||
            $perfilFuncionalidade->perfil->trashed() ||
            $perfilFuncionalidade->perfil->id != $this->id ||
            !$perfilFuncionalidade->funcionalidade ||
            $perfilFuncionalidade->funcionalidade->trashed() ||
            !$perfilFuncionalidade->subFuncionalidades
        ) {
            return;
        }
        $menu = [];
        foreach ($perfilFuncionalidade->subFuncionalidades as $subFuncionalidade) {
            if (!$subFuncionalidade ||
                !$subFuncionalidade->ativado ||
                $subFuncionalidade->trashed() ||
                !$subFuncionalidade->id_perfil ||
                $subFuncionalidade->id_perfil != $this->id ||
                !$subFuncionalidade->id_funcionalidade ||
                !$subFuncionalidade->perfil ||
                $subFuncionalidade->perfil->trashed() ||
                $subFuncionalidade->perfil->id != $this->id ||
                !$subFuncionalidade->funcionalidade ||
                $subFuncionalidade->funcionalidade->trashed() ||
                (
                    !$subFuncionalidade->funcionalidade->url &&
                    !$subFuncionalidade->subFuncionalidades
                )
            ) {
                continue;
            }
            if ($subFuncionalidade->funcionalidade->url) {
                $menu[] = [
                    'name' => $subFuncionalidade->funcionalidade->nome,
                    'i18n' => '',
                    'icon' => ($subFuncionalidade->funcionalidade->icone ? $subFuncionalidade->funcionalidade->icone : null),
                    'iconPack' => ($subFuncionalidade->funcionalidade->icone ? 'fa' : null),
                    'url' => $subFuncionalidade->funcionalidade->url,
                    'slug' => (Helpers::string()::startsWith($subFuncionalidade->funcionalidade->url, '/') ? str_replace('/', '-', substr($subFuncionalidade->funcionalidade->url, 1)) : str_replace('/', '-', $subFuncionalidade->funcionalidade->url)),
                ];
            } elseif ($subFuncionalidade->subFuncionalidades) {
                $subMenu = $this->menuSubFuncionalidades($subFuncionalidade);
                if ($subMenu) {
                    $menu[] = [
                        'name' => $subFuncionalidade->funcionalidade->nome,
                        'i18n' => '',
                        'icon' => ($subFuncionalidade->funcionalidade->icone ? $subFuncionalidade->funcionalidade->icone : null),
                        'iconPack' => ($subFuncionalidade->funcionalidade->icone ? 'fa' : null),
                        'url' => null,
                        'slug' => ($subFuncionalidade->funcionalidade->url ? (Helpers::string()::startsWith($subFuncionalidade->funcionalidade->url, '/') ? str_replace('/', '-', substr($subFuncionalidade->funcionalidade->url, 1)) : str_replace('/', '-', $subFuncionalidade->funcionalidade->url)) : null),
                        'submenu' => $subMenu
                    ];
                }
            }
        }
        return $menu;
    }

    public function menuFuncionalidades()
    {
        if ($this->trashed() ||
            !$this->funcionalidades
        ) {
            return [];
        }

        $funcionalidades = [];
        foreach ($this->funcionalidades as $perfilFuncionalidade) {
            if (!$perfilFuncionalidade ||
                !$perfilFuncionalidade->ativado ||
                $perfilFuncionalidade->trashed() ||
                !$perfilFuncionalidade->id_perfil ||
                $perfilFuncionalidade->id_perfil != $this->id ||
                !$perfilFuncionalidade->id_funcionalidade ||
                !$perfilFuncionalidade->perfil ||
                $perfilFuncionalidade->perfil->trashed() ||
                $perfilFuncionalidade->perfil->id != $this->id ||
                !$perfilFuncionalidade->funcionalidade ||
                $perfilFuncionalidade->funcionalidade->trashed() ||
                !$perfilFuncionalidade->subFuncionalidades
            ) {
                continue;
            }
            $menu = [
                'name' => $perfilFuncionalidade->funcionalidade->nome,
                'i18n' => '',
                'icon' => ($perfilFuncionalidade->funcionalidade->icone ? $perfilFuncionalidade->funcionalidade->icone : null),
                'iconPack' => ($perfilFuncionalidade->funcionalidade->icone ? 'fa' : null),
                'url' => ($perfilFuncionalidade->funcionalidade->url ? $perfilFuncionalidade->funcionalidade->url : null),
            ];
            $subMenu = [];
            foreach ($perfilFuncionalidade->subFuncionalidades as $subFuncionalidade) {
                if (!$subFuncionalidade ||
                    !$subFuncionalidade->ativado ||
                    $subFuncionalidade->trashed() ||
                    !$subFuncionalidade->id_perfil ||
                    $subFuncionalidade->id_perfil != $this->id ||
                    !$subFuncionalidade->id_funcionalidade ||
                    !$subFuncionalidade->perfil ||
                    $subFuncionalidade->perfil->trashed() ||
                    $subFuncionalidade->perfil->id != $this->id ||
                    !$subFuncionalidade->funcionalidade ||
                    $subFuncionalidade->funcionalidade->trashed() ||
                    (
                        !$subFuncionalidade->funcionalidade->url &&
                        !$subFuncionalidade->subFuncionalidades
                    )
                ) {
                    continue;
                }
                if ($subFuncionalidade->funcionalidade->url) {
                    $subMenu[] = [
                        'name' => $subFuncionalidade->funcionalidade->nome,
                        'i18n' => '',
                        'icon' => ($subFuncionalidade->funcionalidade->icone ? $subFuncionalidade->funcionalidade->icone : null),
                        'iconPack' => ($subFuncionalidade->funcionalidade->icone ? 'fa' : null),
                        'url' => $subFuncionalidade->funcionalidade->url,
                        'slug' => (Helpers::string()::startsWith($subFuncionalidade->funcionalidade->url, '/') ? str_replace('/', '-', substr($subFuncionalidade->funcionalidade->url, 1)) : str_replace('/', '-', $subFuncionalidade->funcionalidade->url)),
                    ];
                } elseif ($subFuncionalidade->subFuncionalidades) {
                    $subMenus = $this->menuSubFuncionalidades($subFuncionalidade);
                    if ($subMenus) {
                        $subMenu[] = [
                            'name' => $subFuncionalidade->funcionalidade->nome,
                            'i18n' => '',
                            'icon' => ($subFuncionalidade->funcionalidade->icone ? $subFuncionalidade->funcionalidade->icone : null),
                            'iconPack' => ($subFuncionalidade->funcionalidade->icone ? 'fa' : null),
                            'url' => null,
                            'slug' => ($subFuncionalidade->funcionalidade->url ? (Helpers::string()::startsWith($subFuncionalidade->funcionalidade->url, '/') ? str_replace('/', '-', substr($subFuncionalidade->funcionalidade->url, 1)) : str_replace('/', '-', $subFuncionalidade->funcionalidade->url)) : null),
                            'submenu' => $subMenus
                        ];
                    }
                }
            }
            if ($subMenu) {
                $menu['submenu'] = $subMenu;
                $funcionalidades[] = $menu;
            }
        }
        return $funcionalidades;
    }

    public function salvaMenuFuncionalidades()
    {
        $this->menuFuncionalidadesOriginal = $this->menuFuncionalidades();
    }

    public function valorMenuFuncionalidadesOriginal()
    {
        return $this->menuFuncionalidadesOriginal;
    }

    public function limpaMenuFuncionalidades()
    {
        $this->menuFuncionalidadesOriginal = null;
    }

    public function permissoes()
    {
        if ($this->trashed() ||
            !$this->funcionalidades
        ) {
            return [];
        }
        $regras = ['admin'];
        if ($this->tem_historico_alteracao) {
            $regras[] = 'historicoAlteracao';
        }
        $regras[] = 'modificar';

        return $regras;
    }
}
