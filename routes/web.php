<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
function RouteController($prefix,$controller)
{
	$filter=function($type,$method_name,$repl_prefix=[]) use($prefix,$controller){
		if(strcasecmp($method_name,"index")==0){
			Route::get($prefix,$controller."@".$method_name);
		}
		else if(strpos($method_name,$type)===0 && strripos($method_name,"Middleware")===false){
			if(!$repl_prefix){
				Route::$type($prefix."/".strtolower(str_replace($type,"",$method_name)),$controller."@".$method_name);
			}
			else{
				foreach($repl_prefix as $pre){
					Route::$pre($prefix."/".strtolower(str_replace($type,"",$method_name)),$controller."@".$method_name);
				}
			}
		}
	};

	$class = new ReflectionClass("\App\Http\Controllers\\".$controller);
	$all_methods=$class->getMethods(ReflectionMethod::IS_PUBLIC);
	foreach($all_methods as $method){
		$filter("get",$method->name);
		$filter("post",$method->name);
		$filter("any",$method->name,['get','post']);
	}
}

Route::get('/',"IndexController@anyIndex");
RouteController("/model","ModelController");
RouteController("/category","CategoryController");
RouteController("/content","ContentController");
RouteController("/index","IndexController");
