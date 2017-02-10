<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use App\Models\Menus;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function View($name)
    {
    	$res=(new Menus())->where([['status','=',0]])->get()->toArray();
    	$view=View::make("templates.".$this->getClassName().".".$name);
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

    protected function FilterPrivileges(&$data)
    {
        foreach($data as &$row){
            $row['_internal_field']='11111';
        }
    }
}
