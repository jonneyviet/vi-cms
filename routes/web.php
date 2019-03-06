<?php
use Illuminate\Routing\Router;
$router->group(['prefix' => '/'], function (Router $router) {
	$router->get('/',"HomeController@index");//route Home;
	/*
		Route login for amin
	 */
	$router->get('/login-admin',function(){
		if(!Auth::check()){
			return view("auth.login-admin");
		}else{
			return redirect("/".config("core.admin-prefix"));
		}
	});
	/*
		Router any slug
	 */
	$router->get('/{slug}',"PublicController@uri")
			->where(["slug"=>".*"]);

	/*
		All method post
	 */
	$router->post("/login_admin","Auth\AdminLoginController@login_admin");
	$router->post("/action","ActionController@index");
});
