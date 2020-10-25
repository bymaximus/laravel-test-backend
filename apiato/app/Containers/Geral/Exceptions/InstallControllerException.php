<?php

namespace App\Containers\Geral\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class InstallControllerException extends Exception
{
	public $httpStatusCode = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;

	public $message = 'Erro interno.';
}
