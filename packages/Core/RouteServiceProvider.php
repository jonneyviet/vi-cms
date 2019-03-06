<?php
namespace Packages\Core;
/**
* 
*/
use Packages\Core\RoutingServiceProvider as CoreRoutingServiceProvider;

class RouteServiceProvider extends CoreRoutingServiceProvider
{
	protected $namespace="Packages\Core\Controllers";

	protected function getFrontendRoute(){
		//return __DIR__ . '/Routes/web.php';
	}

	protected function getBackendRoute(){
		return __DIR__ . '/Routes/admin.php';
	}
    protected function getApiRoute(){
        return __DIR__ . '/Routes/api.php';
    }
}