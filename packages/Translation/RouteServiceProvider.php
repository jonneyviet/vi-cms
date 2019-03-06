<?php
namespace Packages\Translation;

/**
* 
*/
use Packages\Core\RoutingServiceProvider as CoreRoutingServiceProvider;

class RouteServiceProvider extends CoreRoutingServiceProvider
{
	protected $namespace="Packages\Translation\Controller";

	protected function getFrontendRoute(){
		
	}
	protected function getBackendRoute(){
		return __DIR__ . '/Routes/admin.php';
	}
	protected function getApiRoute(){

	}
}