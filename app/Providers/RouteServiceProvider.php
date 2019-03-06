<?php
namespace App\Providers;
/**
* 
*/
use Packages\Core\RoutingServiceProvider as CoreRoutingServiceProvider;

class RouteServiceProvider extends CoreRoutingServiceProvider
{
	protected $namespace="App\Http\Controllers";

	protected function getFrontendRoute(){
		return base_path() . '/routes/web.php';
	}

	protected function getBackendRoute(){
	}
	protected function getApiRoute(){
		
	}
}