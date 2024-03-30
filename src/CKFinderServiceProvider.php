<?php

namespace CKSource\CKFinderBridge;

use CKSource\CKFinderBridge\Command\CKFinderDownloadCommand;
use Illuminate\Support\ServiceProvider;

class CKFinderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap.
     */
    public function boot()
    {
        if (config('ckfinder.loadRoutes')) {
            $this->loadRoutesFrom(__DIR__.'/routes.php');
        }
        $this->loadViewsFrom(__DIR__.'/../views', 'ckfinder');

        if ($this->app->runningInConsole()) {
            $this->commands([CKFinderDownloadCommand::class]);

            $this->publishes([
                __DIR__.'/config.php' => config_path('ckfinder.php'),
            ], ['ckfinder-config']);

            $this->publishes([
                __DIR__.'/../public' => public_path('js'),
            ], ['ckfinder-assets']);

            $this->publishes([
                __DIR__.'/../views/setup.blade.php' => resource_path('views/vendor/ckfinder/setup.blade.php'),
                __DIR__.'/../views/browser.blade.php' => resource_path('views/vendor/ckfinder/browser.blade.php'),
            ], ['ckfinder-views']);

            return;
        }

        $this->app->bind('ckfinder.connector', function () {
            if (! class_exists('\CKSource\CKFinder\CKFinder')) {
                throw new \Exception(
                    "Couldn't find CKFinder conector code. ".
                    'Please run `artisan ckfinder:download` command first.'
                );
            }

            $ckfinderConfig = config('ckfinder');

            if (is_null($ckfinderConfig)) {
                throw new \Exception(
                    "Couldn't load CKFinder configuration file. ".
                    'Please run `artisan vendor:publish --tag=ckfinder` command first.'
                );
            }

            $ckfinder = new \CKSource\CKFinder\CKFinder($ckfinderConfig);

            return $ckfinder;
        });
    }
}
