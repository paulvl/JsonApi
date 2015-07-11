<?php

namespace PaulVL\JsonApi;
 
use Illuminate\Support\ServiceProvider;
 
class JsonApiServiceProvider extends ServiceProvider {
 
	/**
	* Bootstrap the application services.
	*
	* @return void
	*/
	public function boot()
	{
		//Publishes package config file to applications config folder
		$this->publishes([__DIR__.'/config/json-api.php' => config_path('json-api.php')]);
	}
	 
	/**
	* Register the application services.
	*
	* @return void
	*/
	public function register()
	{
		$this->registerMakeModel();
        $this->registerMakeController();
		$this->registerMakeNestedController();
		$this->registerMakeBundle();
	}

    /**
     * Register the json-api:make-model generator.
     */
    private function registerMakeModel()
    {
        $this->app->singleton('command.paulvl-jsonapi.make-model', function ($app) {
            return $app['PaulVL\JsonApi\Console\Commands\MakeModel'];
        });
        
        $this->commands('command.paulvl-jsonapi.make-model');
    }

    /**
     * Register the json-api:make-controller generator.
     */
    private function registerMakeController()
    {
        $this->app->singleton('command.paulvl-jsonapi.make-controller', function ($app) {
            return $app['PaulVL\JsonApi\Console\Commands\MakeController'];
        });

        $this->commands('command.paulvl-jsonapi.make-controller');
    }

    /**
     * Register the json-api:make--nested-controller generator.
     */
    private function registerMakeNestedController()
    {
        $this->app->singleton('command.paulvl-jsonapi.make-nested-controller', function ($app) {
            return $app['PaulVL\JsonApi\Console\Commands\MakeController'];
        });

        $this->commands('command.paulvl-jsonapi.make-controller');
    }

    /**
     * Register the json-api:make-bundle generator.
     */
    private function registerMakeBundle()
    {
        $this->app->singleton('command.paulvl-jsonapi.make-bundle', function ($app) {
            return $app['PaulVL\JsonApi\Console\Commands\MakeBundle'];
        });

        $this->commands('command.paulvl-jsonapi.make-bundle');
    }
 
}