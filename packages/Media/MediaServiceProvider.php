<?php
namespace Packages\Media;

/**
* 
*/
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Packages\Media\Repositories\Eloquent\BaseRepository;
use Packages\Media\Repositories\Eloquent\FolderRepository;
use Packages\Media\Repositories\Eloquent\MediaRepository;

class MediaServiceProvider extends LaravelServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
	public function boot(){
		$this->loadViewsFrom(__DIR__."/resources/views","media");//load views
        $this->publishes([
            __DIR__.'/config/media.php'=>config_path('media.php'),//publish config
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');// load Migration
	}
    /**
     * Register the application services.
     *
     * @return void
     */
	public function register(){
		$this->mergeConfigFrom(__DIR__ . '/config/media.php', 'media');
		$this->app->singleton("media",MediaRepository::class);
        $this->app->singleton("Packages\Media\Repositories\Contracts\FolderRepositoryInterface","Packages\Media\Repositories\Eloquent\FolderRepositoryV1");
        $this->app->singleton("Packages\Media\Repositories\Contracts\FileRepositoryInterface","Packages\Media\Repositories\Eloquent\FileRepository");
	}
     public function provides()
    {
        return array_merge(parent::provides(), ['media']);
    }
}