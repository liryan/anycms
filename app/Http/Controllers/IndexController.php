<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends BaseController
{
    public function anyIndex(Request $req)
    {
        return $this->View('index');
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
    protected function View($name)
    {
        $view='';
        if($name[0]=='/'){
            $name=trim($name,"/");
    	    $view=View::make("templates.web.".$name);
        }
        else{
    	    $view=View::make("templates.frontend.".$this->getClassName().".".$name);
        }
    	return $view;
    }
}
