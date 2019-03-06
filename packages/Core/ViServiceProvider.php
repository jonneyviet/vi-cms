<?php

namespace Packages\Core;

/**
* 
*/
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Packages\Core\CoreServiceProvider;
use Packages\Core\EventServiceProvider;
use Packages\Core\RouteServiceProvider as CoreRouteServiceProvider;
use App\Providers\RouteServiceProvider as AppRouteServiceProvider;
use Carbon\Carbon;

class ViServiceProvider extends ServiceProvider
{
	
	function boot(){
	}
	function register(){
		$this->app->register(AppRouteServiceProvider::class);
		$this->app->register(CoreServiceProvider::class);
		$this->app->register(CoreRouteServiceProvider::class);
		$this->app->register(EventServiceProvider::class);//event core

		//load router Translation
		$this->app->register(\Packages\Translation\TranslationServiceProvider::class);
		$this->app->register(\Packages\Translation\RouteServiceProvider::class);
		$this->app->register(\Packages\Translation\LaravelLocalizationServiceProvider::class);

		//media
		$this->app->register(\Packages\Media\MediaServiceProvider::class);
		$this->app->register(\Packages\Media\RouteServiceProvider::class);

		//load router Category
		$this->app->register(\Packages\Category\CategoryServiceProvider::class);
		$this->app->register(\Packages\Category\RouteServiceProvider::class);

        //load router Post
        $this->app->register(\Packages\Post\PostServiceProvider::class);
        $this->app->register(\Packages\Post\RouteServiceProvider::class);
	}
}