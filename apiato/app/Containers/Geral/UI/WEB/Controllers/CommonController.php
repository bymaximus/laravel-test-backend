<?php

namespace App\Containers\Geral\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Auth;
use Exception;

/**
 * Class Controller
 *
 * @package App\Containers\Geral\UI\WEB\Controllers
 */
class CommonController extends WebController
{
    public function guard()
    {
        return Auth::guard('admin');
    }

    public function isLogged()
    {
		if (config('app.env') == 'testing') {
            return true;
		}
        if (!$this->guard()->guest() &&
            $this->guard()->check() &&
            $this->guard()->user() &&
            $this->guard()->user()->id &&
            $this->guard()->payload() &&
            $this->guard()->payload()->get('x-csrf') &&
            $this->guard()->payload()->get('x-sess') &&
            $this->guard()->payload()->get('x-auth') &&
            $this->guard()->payload()->get('sub') &&
            $this->guard()->payload()->get('sub') == $this->guard()->user()->id &&
            $this->guard()->getToken() &&
            $this->guard()->getToken()->get() &&
            session()->token() &&
            session()->get('jwt-x-csrf') &&
            $this->guard()->payload()->get('x-csrf') == session()->get('jwt-x-csrf') &&
            $this->guard()->payload()->get('x-sess') == session()->token() &&
            $this->guard()->user()->validAuthToken($this->guard()->payload()->get('x-auth'))
        ) {
            return true;
        }
        return false;
    }

    public function usuario()
    {
        if ($this->isLogged()) {
            return $this->guard()->user();
        }
        return null;
    }

    public function doLogout()
    {
        try {
            $this->guard()->logout(true);
        } catch (Exception $err) {
        }
        try {
            Auth::logout();
        } catch (Exception $err) {
        }
        try {
            if (session() &&
                session()->get('jwt-x-csrf')
            ) {
                session()->remove('jwt-x-csrf');
            }
        } catch (Exception $err) {
        }
    }
}
