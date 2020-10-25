<?php

namespace App\Containers\Geral\Exceptions;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class InstallControllerAcaoInvalidaException extends InstallControllerException
{
	public $httpStatusCode = SymfonyResponse::HTTP_NOT_FOUND;

	public $message = 'Não Encontrado.';
}
