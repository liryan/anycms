<?php
namespace App\Http\Controllers;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Category;
use App\Models\ConstDefine;
use App\Http\Requests\MenuModify;
use App\Http\Controllers\Widgets\CategoryWidget;

class CategoryController extends Controller
{
    public function index(Request $req)
	{
		$id=$req->get("id",0);
        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $dt=new Category();
            $result=$dt->categories($start,$length,$id);
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
            $cat_widget=new CategoryWidget();
			$models=new DataTable();
            $urlconfig=[
                "url"=>$this->getUrl(),
                "edit_url"=>$this->getUrl("modify"),
                "view_url"=>$this->getUrl("view"),
                "delete_url"=>$this->getUrl("delete"),
                "field_url"=>$this->getUrl("field"),
                "pri"=>'11111',
				"catid"=>$id,
				"models"=>$models->tables(0,200)['data'],
            ];

            $dialog_html=$cat_widget->showEditWidget($urlconfig);
            $list_html=$cat_widget->showListWidget("栏目管理",$urlconfig,$dialog_html);

            return $this->View("index")->with(
                [
                    "table_widget"=>$list_html,
                ]
            );
        }
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
            $tb=new DataTable();
            $re=$tb->getDataById($id);
            $re['action']='edit';
            $re['_token']=csrf_token();
            return json_encode($re);
        }
    }

    public function postModify(Request $req)
    {

        $action=$req->get('action');
        switch($action){
            case "add":
            $name=$req->get('name');
            $note=$req->get('note');
            $setting=$req->get('setting');

            $dtmodel=new DataTable();
            $result=$dtmodel->addTable($name,Array('note'=>$note,'setting'=>$setting));
            return json_encode($result);
            break;

            case "edit":
            $id=$req->get('id');
            $note=$req->get('note');
            $setting=$req->get('setting');

            $dtmodel=new DataTable();
            $result=$dtmodel->editTable($id,Array('note'=>$note,'setting'=>$setting));
            return json_encode($result);
            break;
        }
    }
    public function getDelete(Request $req)
    {
        $id=$req->get('id');
        $dtmodel=new DataTable();
        $result=$dtmodel->deleteTable($id);
        if($result){
            return json_encode(Array('code'=>1));
        }
        return json_encode(Array('code'=>0,'msg'=>'表中含有数据,不能删除'));
    }
}
