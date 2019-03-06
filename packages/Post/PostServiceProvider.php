<?php
namespace Packages\Post;

/**
 *
 */
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class PostServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(){
        $this->loadViewsFrom(__DIR__."/resources/views","post");//load views
        $this->publishes([
            __DIR__.'/config/post.php'=>config_path('post.php'),//publish config
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');// load Migration
        //$this->loadTranslationsFrom(__DIR__.'/resources/lang', 'category');
    }
    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/config/post.php', 'category');
        $this->app->singleton("Packages\Post\Repositories\Contracts\PostRepositoryInterface","Packages\Post\Repositories\Eloquent\PostRepository");
    }
    public function provides()
    {
        return array_merge(parent::provides(), ['post']);
    }
}