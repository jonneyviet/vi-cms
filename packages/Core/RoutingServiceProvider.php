<?php
namespace Packages\Core;

/**
* 
*/
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Application;
use Packages\Media\Models\RouteFiles;
use Packages\Translation\UriLocalizer;
use Packages\Translation\LaravelLocalizationServiceProvider;
use Packages\Translation\Facades\LaravelLocalization;

abstract class RoutingServiceProvider extends LaravelRouteServiceProvider
{

	protected $namespace;
	public function boot()
    {
        parent::boot();
    }
    abstract protected function getBackendRoute();
    abstract protected function getFrontendRoute();
    abstract protected function getApiRoute();

    public function map(Router $router){
    	//load route api
    	$router->group(['namespace'=>$this->namespace],function(Router $router){
    		$this->loadApiRoute($router);
    	});
    	//loa route front end
    	$router->group([
    		'namespace'=>$this->namespace,
    		'prefix'=> LaravelLocalization::setLocale(),
    		'middleware'=>'web'
    	],function($router){
            switch (LaravelLocalization::getType()){
                case "files":
                    $this->loadFiles($router);
                    break;
                case "api":
                     $this->loadApiRoute($router);
                break;
                case "admin":
                     $this->loadBackendRoute($router);
                break;
                default:case "web":
                    $this->loadFrontendRoute($router);
                break;
            }	
    	});

	}
	private function loadFiles(Router $router){
        dd($router);
    }
    private function loadFrontendRoute(Router $router){
    	$fontEnd=$this->getFrontendRoute();
    	if($fontEnd && file_exists($fontEnd)){
    		$router->group([
    			'middleware'=>[],
    		],function(Router $router) use ($fontEnd){
    			require $fontEnd;
    		});
    	}
    }

    private function loadBackendRoute(Router $router){

        $backend = $this->getBackendRoute();

        if ($backend && file_exists($backend)) {
            $router->group([
                'namespace' => 'Admin',
                'prefix' => config('core.admin-prefix'),
                'middleware' => ["admin"],
            ], function (Router $router) use ($backend) {
                require $backend;
            });
        }
    }
    private function loadApiRoute(Router $router){
    	$api=$this->getApiRoute($router);
    	if($api && file_exists($api)){
    		$router->group([
    			'namespace'=>"Api",
    			'prefix'=>'api',
    			'middleware'=>[],
    		],function(Router $router) use ($api){
    			require $api;
    		});
    	}
    }
}