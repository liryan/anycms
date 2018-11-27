<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Models\Category;

class WebController extends BaseController
{
    protected function getNav()
    {
        $cate=new Category();
        $data=$cate->getAllCategory();

        foreach($data as $k=>&$row){
            $this->filterNav($data,$k,$row);
        }
        return $data;
    }

    private function filterNav(&$p,$k,&$row){
        $set=json_decode($row['setting'],true);
        $row['nav']=0;
        if(is_array($set) && isset($set['nav'])){
            $row['nav']=$set['nav'];
        }
        if($row['nav']==0){
            unset($p[$k]);
        }
        else{
            if(isset($row['subdata']) && is_array($row['subdata'])){
                foreach($row['subdata'] as $k=>&$r){
                    $this->filterNav($row['subdata'],$k,$r);
                }
            }
        }
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
    	    $view=View::make("templates.web.".$this->getClassName().".".$name);
        }
    	return $view;
    }
}
