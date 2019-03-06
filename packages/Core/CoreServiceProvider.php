<?php
namespace Packages\Core;

/**
* 
*/
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Packages\Media\Repositories\Eloquent\BaseRepository as MediaBaseRepository;

class CoreServiceProvider extends LaravelServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
	public function boot(){
		$this->loadViewsFrom(__DIR__."/resources/views","core");//load views
        $this->publishes([
            __DIR__.'/config/core.php'=>config_path('core.php'),//publish config
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');// load Migration
	}
	public function register(){
		$this->mergeConfigFrom(__DIR__ . '/config/core.php', 'core');
        $this->app->singleton("Packages\Core\Repositories\Contracts\CoreRepositoryInterface","Packages\Core\Repositories\Eloquent\CoreRepository");
        $this->app->singleton("Packages\Core\Repositories\Contracts\SeoRepositoryInterface","Packages\Core\Repositories\Eloquent\SeoRepository");
	}
}