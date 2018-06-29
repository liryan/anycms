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
        $pridb=new Privileges();
        if($data){
            $pridb->loadUserPri('',$data);
        }
        if(isset($_SERVER['REQUEST_ORIGIN_URI'])){
            $parts=explode("?",$_SERVER['REQUEST_ORIGIN_URI']);
            $parts=explode("/ext/",$parts[0]);
            if(sizeof($parts)==2){
                $id=$pridb->getIdByUrl($parts[1]);
                if($id==0){
                    return redirect("/admin/error?url=".$request->fullUrl());
                }
                if(!$pridb->checkPri($id,Privileges::VIEW,$request->get('admin'))){
                    return redirect("/admin/error?url=".$request->fullUrl());
                }
            }
        }
        if($request->is("admin/content*")){
            $catid=$request->get('catid');
            if(!$pridb->checkPri($catid,Privileges::VIEW,$request->session()->get('admin'))){
                return redirect("/admin/error?url=".$request->fullUrl());
            }
        }
        return $next($request);
    }
}
