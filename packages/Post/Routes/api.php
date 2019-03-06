<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/post'], function (Router $router) {
    $router->get('/getAll',[
        "uses"=>"ApiPostController@getAll",
        "middleware"=>[]
    ]);
    $router->get('/lang',[
        "uses"=>"ApiPostController@lang",
        "middleware"=>[]
    ]);
    $router->get('/getType',[
        "uses"=>"ApiPostController@getType",
        "middleware"=>[]
    ]);
    $router->post('/create',[
        "uses"=>"ApiPostController@create",
        "middleware"=>[]
    ]);
    $router->post('/update',[
        "uses"=>"ApiPostController@update",
        "middleware"=>[]
    ]);
    $router->post('/uploadAvatar',[
        "uses"=>"ApiPostController@uploadAvatar",
        "middleware"=>[]
    ]);
});
