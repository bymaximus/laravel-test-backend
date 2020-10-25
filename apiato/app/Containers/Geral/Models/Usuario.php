<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use App\Containers\Geral\Models\Perfil;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Exception;

/**
 *
 * @property integer $id
 * @property integer $id_perfil
 * @property string $usuario
 * @property string $senha
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_acesso
 * @property string $dt_remocao
 */
class Usuario extends MainModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuario';

    protected $auditExclude = [
        'dt_acesso',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'id_perfil',
        'usuario',
        'senha',
        'dt_criacao',
        'dt_alteracao',
        'dt_acesso',
        'dt_remocao',
    ];

    public $dates = [
        'dt_criacao',
        'dt_alteracao',
        'dt_acesso',
        'dt_remocao',
    ];

    public static $labels = [
        'id' => 'ID',
        'id_perfil' => 'ID Perfil',
        'usuario' => 'Usuário',
        'senha' => 'Senha',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_acesso' => 'Dt. Último Acesso',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'id_perfil' => [
                    'required',
                    'numeric',
                    Rule::exists('perfil', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'usuario' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('usuario', 'usuario')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'senha' => 'required|string|min:3|max:100',
            ];
        } else {
            $self = $this;
            return [
                'id_perfil' => [
                    'required',
                    'numeric',
                    Rule::exists('perfil', 'id')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
                'usuario' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('usuario', 'usuario')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')->where('id', '<>', $self->id);
                    }),
                ],
                'senha' => 'required|string|min:3|max:100',
            ];
        }
    }


    public function perfil()
    {
        return $this->belongsTo('App\Containers\Geral\Models\Perfil', 'id_perfil', 'id')->withTrashed();
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.senha')) {
            $data['new_values']['senha'] = $this->passwordUnHash($this->getAttribute('senha'));
        }
        if (Arr::has($data, 'old_values.senha')) {
            $data['old_values']['senha'] = $this->passwordUnHash($this->getOriginal('senha'));
        }
        if (Arr::has($data, 'new_values.id_perfil')) {
            $data['new_values']['id_perfil'] = $this->getAttribute('id_perfil') . ($this->perfil ? ' (' . $this->perfil->nome . ')' : '');
        }
        if (Arr::has($data, 'old_values.id_perfil')) {
            $data['old_values']['id_perfil'] = $this->getOriginal('id_perfil');
            if ($this->getOriginal('id_perfil') && ($perfilAntigo = Perfil::withTrashed()->find($this->getOriginal('id_perfil')))) {
                $data['old_values']['id_perfil'] .= ' (' . $perfilAntigo->nome . ')';
            }
        }

        return $data;
    }

    public function permissoes()
    {
        if (!$this->id_perfil ||
            !$this->perfil ||
            $this->perfil->trashed() ||
            !$this->perfil->funcionalidades
        ) {
            return [];
        }
        return $this->perfil->permissoes();
    }

    public function funcionalidades()
    {
        if (!$this->id_perfil ||
            !$this->perfil ||
            $this->perfil->trashed() ||
            !$this->perfil->funcionalidades
        ) {
            return [];
        }
        return $this->perfil->menuFuncionalidades();
    }

    public function funcionalidadePermitida($url)
    {
        if (!$this->id_perfil ||
            !$this->perfil ||
            $this->perfil->trashed() ||
            !$this->perfil->funcionalidades
        ) {
            return false;
        }
        return $this->perfil->funcionalidadePermitida($url);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()
    {
        return app('hash')->make($this->passwordUnHash());
    }

    public function passwordHash($password)
    {
        return Helpers::encode()->md5_encrypt($password, 32);
    }

    public function passwordUnHash($password = null)
    {
        if (!$password) {
            $password = $this->senha;
        }
        if (!$password) {
            return null;
        }
        return Helpers::encode()->md5_decrypt($password, 32);
    }

    public static function buildAuthToken($email, $password)
    {
        $data = Helpers::string()::toLower(Helpers::string()::trim($email)) . $password;
        $data = Helpers::encode()->md5_encrypt($data, 32);
        $data = md5($data);
        return app('hash')->make($data);
    }

    public function validAuthToken($token)
    {
        $data = Helpers::string()::toLower(Helpers::string()::trim($this->usuario)) . $this->passwordUnHash();
        $data = Helpers::encode()->md5_encrypt($data, 32);
        $data = md5($data);
        return app('hash')->check($data, $token);
    }

    public function setSenhaAttribute($value)
    {
        if ($value) {
            $value = $this->passwordHash($value);
        }
        $this->attributes['senha'] = $value;
    }

    public function afterAuthenticate()
    {
        try {
            $this->dt_acesso = $this->freshTimestamp();

            if ($this->isValid() &&
                $this->saveOrFail()
            ) {
                return true;
            }
        } catch (Exception $err) {
        }
        return false;
    }
}
