<?php

namespace Equinox\Providers;

use Equinox\Repositories\DataRepository;
use Equinox\Services\General\StorageService;
use Equinox\Services\Repositories\DataService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DataService::class, function () {
            return new DataService(app(DataRepository::class));
        });

        $this->app->bind(StorageService::class, function () {
            return new StorageService();
        });
    }
}
