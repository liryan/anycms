<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'AdminAuth' => \App\Http\Middleware\AdminMiddleware::class,
    ];

    /**
     * dispatchToRouter
     * @ryan
     * override system method to process Custom Menu URL
     * @access protected
     * @return void
     */
    protected function dispatchToRouter()
    {
        $kernal = $this;
        return function ($request) use ($kernal) {
            $request = $kernal->urlFilter($request);
            $this->app->instance('request', $request);
            return $this->router->dispatch($request);
        };
    }
    protected function urlFilter($request)
    {
        if ($request->is("admin/ext/*")) {
            $path = explode("?", $_SERVER['REQUEST_URI']);
            $parts = explode("/ext/", $path[0]);
            if (sizeof($parts) != 2) {
                return $request;
            } else {
                $parts[1] = str_replace("/", "-", $parts[1]);
            }
            $newpath = sprintf("%s/ext/%s%s", $parts[0], $parts[1], sizeof($path) == 2 ? "?" . $path[1] : "");
            $_SERVER['REQUEST_ORIGIN_URI'] = $_SERVER['REQUEST_URI'];
            $_SERVER['REQUEST_URI'] = $newpath;
            $request = Request::capture();
        }
        return $request;
    }
}
