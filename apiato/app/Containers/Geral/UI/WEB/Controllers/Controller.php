<?php

namespace App\Containers\Geral\UI\WEB\Controllers;

use App\Containers\Geral\Exceptions\AlreadyLoggedException;
use App\Containers\Geral\Exceptions\AuthenticationException;
use App\Containers\Geral\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Class Controller
 *
 * @package App\Containers\Geral\UI\WEB\Controllers
 */
class Controller extends CommonController
{
    public function index(Request $request)
    {
        return redirect(Config::get('app.admin_url') . '/login');
    }

    public function loginPage(Request $request)
    {
        return response()->view('geral::login');
    }

    public function login(Request $request)
    {
        if ($this->isLogged()) {
            throw new AlreadyLoggedException();
        }
        $csrfToken = str_random(32);
        session()->put('jwt-x-csrf', $csrfToken);
        if (
            !session()->get('jwt-x-csrf') ||
            session()->get('jwt-x-csrf') != $csrfToken
        ) {
            throw new AuthenticationException('Token inválido.', null, 428);
        }

        $exp = config('jwt.ttl');
        $customClaims = [
            'x-csrf' => session()->get('jwt-x-csrf'),
            'x-sess' => session()->token(),
            'x-auth' => Usuario::buildAuthToken(request('email'), request('password')),
        ];
        if (request('rememberMe')) {
            $exp = (60 * 24) * 30;
            $this->guard()->setTTL($exp);
            $customClaims['x-rememberme'] = true;
        }

        if ($token = $this->guard()->claims($customClaims)->attempt([
            'usuario' => request('email'),
            'password' => request('password'),
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
            if (!$this->guard()->user()->afterAuthenticate()) {
                $this->doLogout();
                throw new AuthenticationException('Não Autorizado #16.');
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
                    'token' => $token,
                ],
                'accessToken' => $token,
            ], 200)->header('Authorization', 'Bearer ' . $token)
                ->withCookie('token', $token, $exp, '/', null, false, true);
        }
        throw new AuthenticationException('Usuário ou senha inválidos.');
    }
}
