<?php

namespace App\Containers\Geral\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends Exception
{
	public $httpStatusCode = Response::HTTP_UNAUTHORIZED;

	public $message = 'Não Autorizado.';
}
