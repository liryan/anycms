<?php

namespace App\Exceptions;

use Exception;
use Config;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->ajax()) {
            return response(json_encode(['code' => 0, 'msg' => $exception->getMessage()]), 200);
        }
        else{
            if (env('APP_DEBUG') == true) {
                return parent::render($request, $exception);
            }
            else{
                if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    if($request->is("admin*")){
                        return redirect(env('404_URL_ADMIN'));
                    }
                    else{
                        return redirect(env('404_URL_WEB'));
                    }
                }
                else{
                    if($request->is("admin*")){
                        return redirect(env('500_URL_ADMIN'));
                    }
                    else{
                        return redirect(env('500_URL_WEB'));
                    }
                }
            }
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        //针对后台的认证跳转
        if ($request->is("admin/*")) {
            return redirect()->guest('admin/login?return_url=' . urlencode($request->fullurl()));
        }
        return redirect()->guest('login?return_url=' . urlencode($request->fullurl()));
    }
}
