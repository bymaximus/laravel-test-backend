<?php

namespace App\Containers\Geral\Exceptions;

use App\Ship\Exceptions\CreateResourceFailedException;
use Illuminate\Support\MessageBag;

class CustomCreateResourceFailedException extends CreateResourceFailedException
{
	public function setErrors($errors = null)
	{
		if (is_null($errors)) {
			$this->errors = new MessageBag();
		} elseif (is_array($errors)) {
			$this->errors = new MessageBag($errors);
		} else {
			$this->errors = $errors;
		}
	}
}
