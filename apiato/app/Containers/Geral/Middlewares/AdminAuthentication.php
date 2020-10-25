<?php

namespace App\Containers\Geral\Middlewares;

use Apiato\Core\Foundation\Facades\Apiato;
use App\Ship\Parents\Middlewares\Middleware;
use Closure;
use Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Session\TokenMismatchException;
use App\Containers\Geral\Exceptions\AuthenticationException;

/**
 * Class AdminAuthentication
 *
 */
class AdminAuthentication extends Middleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * WebAuthentication constructor.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = Auth::guard('admin');
    }

    public function isLogged()
    {
        if (!$this->auth->guest() &&
            $this->auth->check() &&
            $this->auth->user() &&
            $this->auth->user()->id &&
            $this->auth->payload() &&
            $this->auth->payload()->get('x-csrf') &&
            $this->auth->payload()->get('x-sess') &&
            $this->auth->payload()->get('x-auth') &&
            $this->auth->payload()->get('sub') &&
            $this->auth->payload()->get('sub') == $this->auth->user()->id &&
            $this->auth->getToken() &&
            $this->auth->getToken()->get() &&
            session()->token() &&
            session()->get('jwt-x-csrf') &&
            $this->auth->payload()->get('x-csrf') == session()->get('jwt-x-csrf') &&
            $this->auth->payload()->get('x-sess') == session()->token() &&
            $this->auth->user()->validAuthToken($this->auth->payload()->get('x-auth'))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$this->isLogged()) {
                throw new TokenMismatchException('Error #1');
            }
        } catch (\Exception $e) {
            if ($request &&
                (
                    $request->isJson() ||
                    $request->expectsJson() ||
                    $request->wantsJson() ||
                    $request->ajax()
                )
            ) {
                throw new AuthenticationException();
            }
            return redirect('');
        }

        return $next($request);
    }

    public function authenticate($request, array $guards)
    {
        try {
            return parent::authenticate($request, $guards);
        } catch (\Exception $exception) {
            throw new AuthenticationException();
        }
    }
}
