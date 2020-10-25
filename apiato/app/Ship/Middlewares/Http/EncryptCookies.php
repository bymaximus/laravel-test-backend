<?php

namespace App\Ship\Middlewares\Http;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Closure;

/**
 * Class EncryptCookies
 *
 * A.K.A app/Http/Middleware/EncryptCookies.php
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class EncryptCookies extends Middleware
{

    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        /*'token',
        'felipe',
        'felipe2',
        'accordous'*/
    ];

    /*public function handle($request, Closure $next)
    {
        dd('shit', parent::isDisabled('token'));
        return $this->encrypt($next($this->decrypt($request)));
    }*/
}
