<?php

namespace Packages\Translation;

use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;
use Illuminate\Support\Facades\DB;
use Packages\Translation\Commands\FileLoaderCommand;
use Packages\Translation\Repositories\LanguageRepository;
use Packages\Translation\Repositories\TranslationRepository;
use Illuminate\Translation\FileLoader as LaravelFileLoader;
use Packages\Translation\Loaders\FileLoader;
use Packages\Translation\Middleware\TranslationMiddleware;
use Packages\Translation\Routes\ResourceRegistrar;
use Packages\Translation\Loaders\DatabaseLoader;
use Packages\Translation\Loaders\CacheLoader;
use Packages\Translation\Cache\RepositoryFactory as CacheRepositoryFactory;
use Packages\Translation\Commands\CacheFlushCommand;
use Illuminate\Contracts\Cache\Store;
class TranslationServiceProvider extends LaravelTranslationServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__."/resources/views","translation");//load views
        $this->publishes([
            __DIR__.'/config/translation.php'=>config_path('translation.php'),//publish config
        ]);
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');// load Migration
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/translation.php', 'translation');
        
        parent::register();
        $this->registerCacheRepository();
        $this->registerFileLoader();
        $this->app->singleton('translation.uri.localizer', UriLocalizer::class);

        /*
            registerCacheFlusher error. Can not call class cache/ Not use command translator:flush
         */
         //$this->registerCacheFlusher(); 
    }

    public function provides()
    {
        return array_merge(parent::provides(), ['translation.cache.repository', 'translation.uri.localizer', 'translation.loader']);
    }
     /**
     * Register the translation line loader.
     * Method in file TranslationServiceProvider.php in Laravel
     *
     * @return void
     */
    protected function registerLoader()
    {
        $app=$this->app;
        $this->app->singleton('translation.loader', function ($app) {
            $defaultLocale = $app['config']->get('app.locale');
            $loader        = null;
            $source        = $app['config']->get('translation.source');
            switch ($source) {
                case 'database':
                    $loader= new DatabaseLoader($defaultLocale,$app->make(TranslationRepository::class));
                    break;
                default:case 'files':
                    $laravelFileLoader = new LaravelFileLoader($app['files'], $app->basePath() . '/resources/lang');
                    $loader            = new FileLoader($defaultLocale, $laravelFileLoader);
                    break;
            }
            //dd($app["config"]->get("translation.cache.enabled"));
            if((bool)$app["config"]->get("translation.cache.enabled")){
                $loader     =   new CacheLoader($defaultLocale,$app["translation.cache.repository"],$loader,$app['config']->get('translator.cache.timeout'));
            }
            return $loader;
        });
    }
    public function registerCacheRepository()
    {
        $this->app->singleton('translation.cache.repository', function ($app) {
            $cacheStore = $app['cache']->getStore();
            return CacheRepositoryFactory::make($cacheStore, $app['config']->get('translation.cache.suffix'));
        });
    }
    protected function registerFileLoader(){
        $app=$this->app;
        $defaultLocale         = $app['config']->get('app.locale');
        $translationsPath      = $app->basePath() . '/resources/lang';
        $languageRepository    = $app->make(LanguageRepository::class);
        $translationRepository = $app->make(TranslationRepository::class);
        $command               = new FileLoaderCommand($languageRepository, $translationRepository, $app['files'], $translationsPath, $defaultLocale);
        $this->app['command.translator:load'] = $command;
        $this->commands('command.translator:load');
    }
    public function registerCacheFlusher(){
        //dd($this->app);
        $command = new CacheFlushCommand($this->app['translation.cache.repository'], $this->app['config']->get('translation.cache.enabled'));
        $this->app['command.translator:flush'] = $command;
        $this->commands('command.translator:flush');
    }
}













