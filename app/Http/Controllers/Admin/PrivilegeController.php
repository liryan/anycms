<?php
namespace App\Http\Controllers\Admin;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Category;
use App\Models\Privileges;
use App\Models\ConstDefine;
use App\Http\Requests\MenuModify;
use App\Http\Controllers\Admin\Widgets\PrivilegeWidget;
use App\Http\Controllers\Admin\Widgets\CategoryWidget;

class PrivilegeController extends AdminController
{

    public function index(Request $req)
	{
		$id=$req->get("id",0);
        $model=new Privileges();
        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $result=$model->getRoles($start,$length,$id);
			foreach($result['data'] as &$row){
				$row['_internal_field']='';
			}
            $data=Array(
                "draw"=>$draw,
                "recordsTotal"=>$result['total'],
                "recordsFiltered"=>$result['total'],
                "data"=>$result['data'],
            );
            return json_encode($data);
        }
        else{
            $pri_widget=new PrivilegeWidget();
            $urlconfig=[
                "url"=>$this->getUrl()."?id=$id",
                "edit_url"=>$this->getUrl("modify"),
                "view_url"=>$this->getUrl("view"),
                "open_url"=>$this->geturl("pri"),
                "delete_url"=>$this->getUrl("delete"),
                "field_url"=>$this->getUrl("field"),
                "pri"=>'11110',
				"id"=>$id
            ];

            $dialog_html=$pri_widget->showEditWidget($urlconfig);
            $list_html=$pri_widget->showPriListWidget("角色管理",$urlconfig,$dialog_html);
            return $this->View("index")->with(
                [
                    "table_widget"=>$list_html,
                ]
            );
        }
	}


    public function getMenus(Request $req)
    {
    }

    public function getView(Request $req)
    {
        $id=$req->get('id');
        if($id==0){
            $re['action']='add';
            $re['_token']=csrf_token();
            return json_encode($re);
        }
        else{
            $cate=new Category();
            $re=$cate->getDataById($id);
            $widgets=new CategoryWidget();
            $widgets->tranformSetting($re,false);
            $re['action']='edit';
            $re['_token']=csrf_token();
            return json_encode($re);
        }
    }

    public function postModify(Request $req)
    {
        $id=$req->get("id",0);
        $action=$req->get('action');
        switch($action){
            case "add":
            $data=Array('name'=>$req->get('name'));
            $model=new Privileges();
            $result=$model->addRole($data);
            return json_encode($result);
            break;

            case "edit":
            $model=new Privileges();
            $data=Array('name'=>$req->get('name'));
            $result=$model->editRole($id,$data);
            return json_encode($result);
            break;
        }
    }

    public function getDelete(Request $req)
    {
        $id=$req->get('id');
        $model=new Privileges();
        $result=$model->deleteRole($id);
        if($result){
            return json_encode(Array('code'=>1));
        }
        return json_encode(Array('code'=>0,'msg'=>'删除失败'));
    }

    private function readNode($row,&$re)
    {
        $re[]=Array('name'=>$row['name'],'note'=>$row['note'],'id'=>$row['id']);
        if(!isset($row['subdata'])){
            return;
        }
        foreach($row['subdata'] as $node){
            $this->readNode($node,$re);
        }
    }


    public function getPri(Request $req)
    {
        $cate=new Category();
        $tab=new DataTable();
        $models=$tab->tables(0,1000);
        $priCats=$cate->getAllCategory();
        $re=[];
        foreach($priCats as $row){
            $this->readNode($row,$re);
        } 
        $view=$this->View("pri");
        $menu=Array(['name'=>'频道管理','note'=>"/admin/category"]);
        return $view->with("pricats",$re)->with('primodels',$models['data']);
    }
}
