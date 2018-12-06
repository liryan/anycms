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
function RouteController($prefix, $controller, $namespace = '')
{
    $filter = function ($type, $method_name, $repl_prefix = []) use ($prefix, $controller) {
        if (strcasecmp($method_name, "index") == 0) {
            Route::get($prefix, $controller . "@" . $method_name);
        } else if (strpos($method_name, $type) === 0 && strripos($method_name, "Middleware") === false) {
            $path = str_replace($type, "", $method_name);
            $pos = strpos($controller, "\\ExtController");
            if ($pos !== false) {
                $path = preg_replace_callback("/([A-Z]+[a-z]+)/", function ($match) {
                    return "-" . strtolower($match[0]);
                }, $path);
                $path = trim($path, "-");
            } else {
                $path = strtolower($path);
            }
            if (!$repl_prefix) {
                Route::$type($prefix . "/" . $path, $controller . "@" . $method_name);
            } else {
                foreach ($repl_prefix as $pre) {
                    Route::$pre($prefix . "/" . $path, $controller . "@" . $method_name);
                }
            }
        }
    };

    $class = new ReflectionClass('\App\Http\Controllers\\' . $controller);
    $all_methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($all_methods as $method) {
        $filter("get", $method->name);
        $filter("post", $method->name);
        $filter("any", $method->name, ['get', 'post']);
    }
}

Route::group(['prefix' => 'admin', 'middleware' => ['AdminAuth']], function () {
    //RouteController("/site", "Admin\\SiteController");
    RouteController("/model", "Admin\\ModelController");
    RouteController("/category", "Admin\\CategoryController");
    RouteController("/content", "Admin\\ContentController");
    RouteController("/const", "Admin\\ConstController");
    RouteController("/privilege", "Admin\\PrivilegeController");
    RouteController("/account", "Admin\\AccountController");
    RouteController("/personal", "Admin\\PersonalController");
    RouteController("/menu", "Admin\\MenuController");
    RouteController("/stat", "Admin\\StatController");
    RouteController("/ext", "Admin\\ExtController");
    Route::get("/index", "Admin\\PersonalController@index");
    Route::get("/personal/welcome", "Admin\\PersonalController@index");
    Route::get("/login", "Admin\\UserController@getLogin");
    Route::get("/logout", "Admin\\UserController@getLogin");
    Route::post("/dologin", "Admin\\UserController@postDoLogin");
    Route::get("/error", "Admin\\AdminController@authFailed");
    Route::get("/error404", "Admin\\AdminController@notFoundError");
    Route::get("/", "Admin\\UserController@checkLogin");
});

Route::get('/', "IndexController@index");
Route::get('/c/{id}', "IndexController@category");
Route::get('/c/{id}-{page}', "IndexController@category");
Route::get('/a/{id}', "IndexController@article");
