<?php
namespace App\Http\Controllers\Admin;

use Auth;
use Redirect;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use App\Models\Category;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $breadcrumb; //面包屑导航数组

    protected function View($name)
    {
        $view='';
        if($name[0]=='/'){
            $name=trim($name,"/");
    	    $view=View::make("templates.".$name);
        }
        else{
    	    $view=View::make("templates.".$this->getClassName().".".$name);
        }
    	return $view;
    }

    protected function getClassName()
    {
    	$class_name=get_class($this);
    	$path=explode("\\",$class_name);
    	if($path){
    		$class_name=array_pop($path);
    	}
    	return str_replace("controller","",strtolower($class_name));
    }

    protected function getUrl($method='')
    {
        return '/'.$this->getClassName().'/'.strtolower($method);
    }

    protected function widget($name){
        return View::make("widgets.".$name);
    }

    protected function ajax($code,$msg,$data=[])
    {
        return json_encode(['code'=>$code,'msg'=>$msg,'data'=>$data]);
    }

    protected function getValuesByPrefix($prefix,$onlyValues=true)
    {
        $data=[];
        foreach($_POST as $k=>$v){
            if(strpos($k,$prefix)!==false){
                $key=str_replace($prefix,"",$k);
                if($onlyValues){
                    if($v && $key){
                        $data[]=$key;
                    }
                }
                else{
                    $data[$key]=$k;
                }
            }
        }
        return $data;
    }
}
