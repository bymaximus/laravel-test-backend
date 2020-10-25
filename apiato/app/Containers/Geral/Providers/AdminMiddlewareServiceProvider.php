<?php

namespace App\Containers\Geral\Providers;

use App\Containers\Geral\Middlewares\AdminAuthentication;
use App\Ship\Parents\Providers\MiddlewareProvider;

class AdminMiddlewareServiceProvider extends MiddlewareProvider
{
	/**
	 * Register Middleware's
	 *
	 * @var  array
	 */
	protected $middlewares = [
		//AdminAuthentication::class,
	];

	/**
	 * Register Container Middleware Groups
	 *
	 * @var  array
	 */
	protected $middlewareGroups = [
		'web' => [
			//AdminAuthentication::class,
		],
		'api' => [
			// ..
		],
	];

	protected $routeMiddleware = [
		// apiato User Authentication middleware for Web Pages
		'auth.admin' => AdminAuthentication::class,

		// ..
	];
}
