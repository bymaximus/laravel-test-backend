<?php

namespace App\Ship\Providers;

use App\Ship\Parents\Providers\MainProvider;

/**
 * Class ShipProvider
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class ShipProvider extends MainProvider
{
    /**
     * Register any Service Providers on the Ship layer (including third party packages).
     *
     * @var array
     */
    public $serviceProviders = [
        \RachidLaasri\LaravelInstaller\Providers\LaravelInstallerServiceProvider::class,
        \Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider::class,
        \Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
        \Yajra\DataTables\DataTablesServiceProvider::class,
        \Yajra\DataTables\FractalServiceProvider::class,
        \Collective\Html\HtmlServiceProvider::class,
        \Lucascudo\LaravelPtBRLocalization\LaravelPtBRLocalizationServiceProvider::class,
        \OwenIt\Auditing\AuditingServiceProvider::class,
        \Felixkiss\UniqueWithValidator\ServiceProvider::class,
    ];

    /**
     * Register any Alias on the Ship layer (including third party packages).
     *
     * @var  array
     */
    protected $aliases = [
        'DataTables' => \Yajra\DataTables\Facades\DataTables::class,
        'Form' => \Collective\Html\FormFacade::class,
        'Html' => \Collective\Html\HtmlFacade::class,
    ];

    public function __construct()
    {
        parent::__construct(app());

        if (class_exists('Barryvdh\Debugbar\ServiceProvider')) {
            $this->serviceProviders[] = \Barryvdh\Debugbar\ServiceProvider::class;
        }

        if (class_exists('Barryvdh\Debugbar\Facade')) {
            $this->aliases[] = \Barryvdh\Debugbar\Facade::class;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ...
        parent::boot();
        // ...
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Load the ide-helper service provider only in non production environments.
         */
        if ($this->app->environment() !== 'production' && class_exists('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        parent::register();
    }
}
