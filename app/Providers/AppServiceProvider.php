<?php

namespace Equinox\Providers;

use Equinox\Repositories\DataRepository;
use Equinox\Services\Repositories\DataService;
use Equinox\Services\Structure\RecordService;
use Equinox\Services\Structure\StorageService;
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

        $this->app->bind(RecordService::class, function () {
            return new RecordService();
        });
    }
}
