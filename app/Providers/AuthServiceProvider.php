<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //验证是前台登录还是后台登录
        $request = Request::capture();
        if ($request->is("admin/*") || $request->path() == 'admin') {
            app()['config']->set("session.cookie", app()['config']->get('session.admin_cookie_name'));
            app()['config']->set("auth.guards.web.provider", "admin");
        } else {
            app()['config']->set("session.cookie", app()['config']->get('session.web_cookie_name'));
        }
        $this->registerPolicies();
    }
}
