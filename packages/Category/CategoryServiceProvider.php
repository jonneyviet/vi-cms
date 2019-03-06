<?php
namespace Packages\Category;

/**
* 
*/
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class CategoryServiceProvider extends LaravelServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
	public function boot(){
		$this->loadViewsFrom(__DIR__."/resources/views","category");//load views
        $this->publishes([
            __DIR__.'/config/category.php'=>config_path('category.php'),//publish config
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');// load Migration
        //$this->loadTranslationsFrom(__DIR__.'/resources/lang', 'category');
	}
	public function register(){
        $this->mergeConfigFrom(__DIR__ . '/config/category.php', 'category');
        $this->app->singleton("Packages\Category\Repositories\Contracts\CategoryRepositoryInterface","Packages\Category\Repositories\Eloquent\CategoryRepository");
	}
     public function provides()
    {
        return array_merge(parent::provides(), ['category']);
    }
}