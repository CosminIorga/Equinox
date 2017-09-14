<?php

namespace App\Providers;


use Equinox\Repositories\DataRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DataRepository::class, function () {
            return new DataRepository();

            //TODO: Create parallel and serial repository
//            $parallelProcessingFlag = config('common.gearman_parallel_processing');
//
//            if ($parallelProcessingFlag) {
//                return new ParallelDataRepository();
//            }
//
//            return new SerialDataRepository();
        });

//        $this->app->singleton(RedisRepository::class, function () {
//            return new RedisRepository();
//        });
    }
}
