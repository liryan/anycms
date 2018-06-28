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
use App\Models\Privileges;
use App\Models\Category;
class AdminController extends Controller
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

    protected function getShortPath()
    {
        $path=explode("?",$_SERVER['REQUEST_URI']);
        $path=explode("/",$path[0]);
        $curpath="";
        $counter=0;
        foreach($path as $p){
            if(!$p){
                continue;
            }
            $counter++;
            $curpath.="/".$p;
            if($counter >= 2){
                break;
            }
        }
        return $curpath;
    }

    protected function View($name)
    {
    	$view=parent::View($name);
        $view->with("path",$this->getShortPath());
        $view->with("admin",session()->get('admin'));
        $view->with('breadcrumb',is_array($this->breadcrumb)?$this->breadcrumb:Array());
        $view->with('sys_menus',$this->getMenu(true));
        $view->with('categories',$this->getCategoryMenu());
        $view->with('user_menus',$this->getCustomMenu());
        $view->with('username',$this->user()->name);
    	return $view;
    }

    protected function getUrl($method='')
    {
        return '/'.$this->prefix."/".$this->getClassName().'/'.strtolower($method);
    }


    protected function widget($name)
    {
        return View::make("widgets.".$name);
    }

    /**
     * 获取列表后面的权限
     * @method FilterTablePrivileges
     * @param  [type] $data [description]
     */
    protected function FilterTablePrivileges()
    {
        return "11111";
    }

    protected function filterCategory(&$row)
    {
        $pridb=new Privileges();
        if(isset($row['subdata']) && is_array($row['subdata'])>0){
            foreach($row['subdata'] as $k=>&$r){
                if(false==$pridb->checkPri($r['id'],Privileges::VIEW)){
                    unset($row['subdata'][$k]);
                }
                else{
                    $this->filterCategory($r);
                }
            }
        }
    }

    protected function getMenu($isSystem)
    {
        $pridb=new Privileges();
        $menus=$pridb->getMenus($isSystem?Privileges::MENU_SYS_ID:Privileges::MENU_USR_ID,0,100);
        return $menus['data'];
    }

    protected function getCustomMenu()
    {
        $pridb=new Privileges();
        $data=$pridb->getAllMenus();
        if(!$data){
            $data=Array();
        }
        $re=[];
        if(session()->get('admin')==0){
            foreach($data as $k=>&$r){
                if(false==$pridb->checkPri($r['id'],Privileges::VIEW)){
                    unset($data[$k]);
                }
                else{
                    $this->filterCategory($r);
                }
            }
        }
        foreach($data as $row){
            $this->treeToArray($re,$row);
        }
        return $re;
    }
    protected function getCategoryMenu()
    {
        $cate=new Category();
        $data=$cate->getAllCategory();
        if(!$data){
            $data=Array();
        }
        $re=[];
        if(session()->get('admin')==0){
            $pridb=new Privileges();
            foreach($data as $k=>&$r){
                if(false==$pridb->checkPri($r['id'],Privileges::VIEW)){
                    unset($data[$k]);
                }
                else{
                    $this->filterCategory($r);
                }
            }
        }
        foreach($data as $row){
            $this->treeToArray($re,$row);
        }
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

    public function authFailed(Request $req){
        return $this->error($req,"访问出错了，请确定有权限访问当前URL");
    }

    protected function error(Request $req,$msg)
    {
        if($req->ajax()){
            return $this->ajax(0,$msg);
        }
        else{
            return $this->View('/common.error')->with('msg',$msg);
        }
    }
}
