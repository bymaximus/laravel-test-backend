<?php

namespace App\Containers\Geral\Providers;

use Apiato\Core\Loaders\RoutesLoaderTrait;
use App\Ship\Parents\Providers\AuthProvider as ParentAuthProvider;

class AuthProvider extends ParentAuthProvider
{
	use RoutesLoaderTrait;

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();
		$this->app->auth->provider('customEloquent', function ($app, array $config) {
			return new CustomEloquentUserProvider($app['hash'], $config['model']);
		});
	}
}
