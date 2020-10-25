<?php

namespace App\Containers\Geral\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response;

class AlreadyLoggedException extends Exception
{
	public $httpStatusCode = Response::HTTP_CONFLICT;

	public $message = 'Já logado.';
}
