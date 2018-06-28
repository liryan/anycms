<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Privileges;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data=$request->session()->get('pridata');
        if($data){
            $pridb=new Privileges();
            $pridb->loadUserPri('',$data);
        }
        if(isset($_SERVER['REQUEST_ORIGIN_URI'])){
            $parts=explode("?",$_SERVER['REQUEST_ORIGIN_URI']);
            $parts=explode("/ext/",$parts[0]);
            if(sizeof($parts)==2){
                $id=$pridb->getIdByUrl($parts[1]);
                if($id==0){
                    return redirect("/admin/error");
                }
                if(!$pridb->checkPri($id,Privileges::VIEW)){
                    return redirect("/admin/error");
                }
            }
            else{
                print_r($parts);
            }
        }
        return $next($request);
    }
}
