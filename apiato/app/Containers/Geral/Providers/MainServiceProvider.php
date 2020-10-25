<?php

namespace App\Containers\Geral\Providers;

use Illuminate\Console\Scheduling\Schedule;
use App\Ship\Parents\Providers\MainProvider;
use App\Containers\Geral\Helpers\Helpers;
use App\Containers\Geral\Helpers\StringHelper;
use App\Containers\Geral\Helpers\FileSystemHelper;

/**
 * Class MainServiceProvider.
 *
 * The Main Service Provider of this container, it will be automatically registered in the framework.
 */
class MainServiceProvider extends MainProvider
{
    /**
     * Container Service Providers.
     *
     * @var array
     */
    public $serviceProviders = [
        AuthProvider::class,
        AdminMiddlewareServiceProvider::class,
    ];

    /**
     * Container Aliases
     *
     * @var  array
     */
    public $aliases = [
        'Helpers' => Helpers::class,
        'StringHelper' => StringHelper::class,
        'FileSystemHelper' => FileSystemHelper::class,
    ];

    public function boot()
    {
        parent::boot();
    }

    /**
     * Register anything in the container.
     */
    public function register()
    {
        parent::register();

        // $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // ...
    }
}
