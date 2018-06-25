<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Config;
use App;
use Tools;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\Category;
class AdminController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $breadcrumb; //面包屑导航数组
    protected $user;
    protected $prefix='admin';
	public function __construct()
	{
        //修改admin认证的驱动
		$this->middleware('auth');
	}
    protected function user()
    {
        if(!$this->user)
            $this->user=Auth::user();
        return $this->user;
    }

    protected function View($name)
    {
        $path=explode("/",$_SERVER['REQUEST_URI']);
    	$view=View::make("templates.".$this->getClassName().".".$name);
        $view->with("path",$path[2]);
        $view->with('breadcrumb',is_array($this->breadcrumb)?$this->breadcrumb:Array());
        $view->with('categories',$this->getCategoryMenu());
        $view->with('username',$this->user()->name);
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
        return '/'.$this->prefix."/".$this->getClassName().'/'.strtolower($method);
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
        if(!$data){
            $data=Array();
        }
        $re=[];
        foreach($data as $row)
            $this->treeToArray($re,$row);
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
