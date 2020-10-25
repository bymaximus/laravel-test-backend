<?php

namespace App\Containers\Geral\Exceptions\Formatters;

use Apiato\Core\Exceptions\Formatters\ExceptionsFormatter as CoreExceptionsFormatter;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class HttpExceptionFormatter
 *
 * @author  Johannes Schobel <johannes.schobel@googlemail.com>
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class HttpExceptionFormatter extends CoreExceptionsFormatter
{
	/**
	 * Status Code.
	 *
	 * @var  int
	 */
	public $statusCode;

	/**
	 * @param \Exception                    $exception
	 * @param \Illuminate\Http\JsonResponse $response
	 *
	 * @return  array
	 */
	public function responseData(Exception $exception, JsonResponse $response)
	{
		// set status code from the exception
		$this->statusCode = $exception->getStatusCode();

		$errors = [];
		if (!$errors &&
			method_exists($exception, 'getErrors')
		) {
			$errors = $exception->getErrors();
		}
		if (!$errors &&
			method_exists($exception, 'errors')
		) {
			$errors = $exception->errors();
		}

		return [
			'code' => $exception->getCode(),
			'message' => $exception->getMessage(),
			'errors' => $errors,
			'status_code' => $this->statusCode,
		];
	}

	/**
	 * @param \Exception                    $exception
	 * @param \Illuminate\Http\JsonResponse $response
	 *
	 * @return  \Illuminate\Http\JsonResponse
	 */
	public function modifyResponse(Exception $exception, JsonResponse $response)
	{
		// append exception headers to the response headers.
		if (count($headers = $exception->getHeaders())) {
			$response->headers->add($headers);
		}

		return $response;
	}

	/**
	 * @return  int
	 */
	public function statusCode()
	{
		return $this->statusCode;
	}
}
