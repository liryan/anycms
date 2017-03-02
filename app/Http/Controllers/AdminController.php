<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use App\Models\Category;
class AdminController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $breadcrumb; //面包屑导航数组
	public function __construct()
	{
		$this->middleware('auth');
	}

    protected function View($name)
    {
    	$view=View::make("templates.".$this->getClassName().".".$name);
        $view->with('breadcrumb',is_array($this->breadcrumb)?$this->breadcrumb:Array());
        $view->with('categories',$this->getCategoryMenu());
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
    /**
     * 获取列表后面的权限
     * @method FilterTablePrivileges
     * @param  [type]                $data [description]
     */
    protected function FilterTablePrivileges()
    {
        return "11111";
    }

    protected function getCategoryMenu()
    {
        $cate=new Category();
        $data=$cate->getAllCategory();
        $re=[];
        $this->treeToArray($re,$data[0]);
        return $re;
    }

    protected function treeToArray(&$data,$treedata)
    {
        $subdata=isset($treedata['subdata'])?$treedata['subdata']:[];
        if(is_array($subdata) && sizeof($subdata)>0){
            $treedata['subdata']='>';
            $data[]=$treedata;
            foreach($subdata as $sub){
                $this->treeToArray($data,$sub);
            }
            $data[]=['note'=>'close','subdata'=>'<'];
        }
        else{
            $treedata['subdata']='|';
            $data[]=$treedata;
        }
    }
}
