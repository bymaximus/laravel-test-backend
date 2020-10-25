<?php

namespace App\Containers\Geral\UI\WEB\Controllers;

use Illuminate\Http\Request;
use App\Containers\Geral\Exceptions\AuthenticationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Containers\Geral\Models\Usuario;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Class DashboardController
 *
 * @package App\Containers\Geral\UI\WEB\Controllers
 */
class DashboardController extends CommonController
{
    public function index(Request $request)
    {
        return view('geral::dashboard');
    }

    public function refreshLogin(Request $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }

        $token = request('token');
        if (!$token) {
            if ($request->hasCookie('token')) {
                $token = $request->cookie('token');
            }
        }

        if (!$token ||
            $token != $this->guard()->getToken()->get()
        ) {
            throw new AuthenticationException();
        }

        $exp = config('jwt.ttl');
        $rememberMe = $this->guard()->payload()->get('x-rememberme');
        $csrf = $this->guard()->payload()->get('x-csrf');
        $sess = $this->guard()->payload()->get('x-sess');
        $auth = $this->guard()->payload()->get('x-auth');
        if ($rememberMe) {
            $exp = (60 * 24) * 30;
            $this->guard()->setTTL($exp);
        }

        try {
            $newToken = $this->guard()->refresh(false, false);
            $this->guard()->setToken($newToken);
        } catch (TokenExpiredException $err) {
            $customClaims = [
                //'sub' => $user->id,
                'x-csrf' => $csrf,
                'x-sess' => $sess,
                'x-auth' => $auth,
            ];
            if ($rememberMe) {
                $customClaims['x-rememberme'] = true;
            }
            if ($newToken = $this->guard()->claims($customClaims)->attempt([
                'usuario' => $this->guard()->user()->usuario,
                'password' => $this->guard()->user()->passwordUnHash(),
            ])) {
                if (!$this->guard()->check()) {
                    $this->doLogout();
                    throw new AuthenticationException();
                }
                if (!$this->guard()->user()) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado.');
                }
                if (!$this->guard()->user()->id) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #1.');
                }
                if (!$this->guard()->user()->id_perfil) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #2.');
                }
                if (!$this->guard()->user()->perfil) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #3.');
                }
                if ($this->guard()->user()->perfil->trashed()) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #4.');
                }
                if (!$this->guard()->payload()) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #5.');
                }
                if (!$this->guard()->payload()->get('x-csrf')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #6.');
                }
                if (!$this->guard()->payload()->get('x-sess')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #7.');
                }
                if (!session()->token()) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #8.');
                }
                if (!session()->get('jwt-x-csrf')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #9.');
                }
                if ($this->guard()->payload()->get('x-csrf') != session()->get('jwt-x-csrf')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #10.');
                }
                if ($this->guard()->payload()->get('x-sess') != session()->token()) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #11.');
                }
                if (!$this->guard()->payload()->get('sub')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #12.');
                }
                if ($this->guard()->payload()->get('sub') != $this->guard()->user()->id) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #13.');
                }
                if (!$this->guard()->payload()->get('x-auth')) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #14.');
                }
                if (!$this->guard()->user()->validAuthToken($this->guard()->payload()->get('x-auth'))) {
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #15.');
                }
                $this->guard()->user()->disableAuditing();
                if (!$this->guard()->user()->afterAuthenticate()) {
                    $this->guard()->user()->enableAuditing();
                    $this->doLogout();
                    throw new AuthenticationException('Não Autorizado #16.');
                }
                $this->guard()->user()->enableAuditing();
            } else {
                throw new AuthenticationException('Usuário ou senha inválidos.');
            }
        }

        return response()->json([
            'userData' => [
                'uid' => $this->guard()->user()->id,
                'idUsuario' => $this->guard()->user()->id,
                'idPerfil' => $this->guard()->user()->id_perfil,
                'displayName' => $this->guard()->user()->usuario,
                'email' => $this->guard()->user()->usuario,
                'phoneNumber' => '',
                'about' => '',
                'status' => 'online',
                'userRole' => $this->guard()->user()->permissoes(),
                'menu' => $this->guard()->user()->funcionalidades(),
                'photoURL' => '/images/portrait/small/avatar-s-5.jpg',
                'providerId' => 'jwt',
                'token' => $newToken
            ],
            'accessToken' => $newToken
        ], 200)->header('Authorization', 'Bearer ' . $newToken)
                        ->withCookie('token', $newToken, $exp, '/', null, false, true);
    }

    public function logout(Request $request)
    {
        if (!$this->isLogged()) {
            throw new AuthenticationException();
        }
        $this->doLogout();
        if ($request->isJson()) {
            return response()->json([
                'loggedOut' => true
            ], 200)->withCookie('token', null, -2628000, '/', null, false, true);
        } else {
            return redirect(Config::get('app.admin_url') . '/')->withCookie('token', null, -2628000, '/', null, false, true);
        }
    }
}
