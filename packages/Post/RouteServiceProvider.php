<?php
namespace Packages\Post;

/**
 *
 */
use Packages\Core\RoutingServiceProvider as CoreRoutingServiceProvider;

class RouteServiceProvider extends CoreRoutingServiceProvider
{
    protected $namespace="Packages\Post\Controller";

    protected function getFrontendRoute(){

    }
    protected function getBackendRoute(){
        return __DIR__ . '/Routes/admin.php';
    }
    protected function getApiRoute(){
        return __DIR__ . '/Routes/api.php';
    }
}