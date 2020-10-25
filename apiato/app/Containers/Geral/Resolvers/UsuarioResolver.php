<?php

namespace App\Containers\Geral\Resolvers;

use Auth;
use Exception;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class UsuarioResolver implements \OwenIt\Auditing\Contracts\UserResolver
{
    public static function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * {@inheritdoc}
     */
    public static function resolve()
    {
        if (!self::guard()->guest() &&
            self::guard()->check() &&
            self::guard()->user() &&
            self::guard()->user()->id &&
            self::guard()->payload() &&
            self::guard()->payload()->get('x-csrf') &&
            self::guard()->payload()->get('x-sess') &&
            self::guard()->payload()->get('x-auth') &&
            self::guard()->payload()->get('sub') &&
            self::guard()->payload()->get('sub') == self::guard()->user()->id &&
            self::guard()->getToken() &&
            self::guard()->getToken()->get()
        ) {
            return self::guard()->user();
        }
    }
}
