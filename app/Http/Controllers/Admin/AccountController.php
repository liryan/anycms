<?php
namespace App\Http\Controllers\Admin;
use Config;
use DB;
use Redirect;
use App\Models\DataTable;
use App\Models\User;
use App\Http\Controllers\Admin\Widgets\UserWidget;
use Illuminate\Http\Request;

class AccountController extends AdminController
{
    public function index(Request $req)
    {
		$id=$req->get("id",0);
		$dt=new DataTable();
        $model=new User();
        $widget=new UserWidget();

        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $result=$model->getList($start,$length);
			foreach($result['data'] as &$row){
				$row->_internal_field='';
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
            $urlconfig=[
                "url"=>$this->getUrl()."?id=$id",
                "edit_url"=>$this->getUrl("modify")."?id=$id",
                "view_url"=>$this->getUrl("view")."?id=$id",
                "open_url"=>$this->geturl("")."?id=$id",
                "delete_url"=>$this->getUrl("delete")."?id=$id",
                "field_url"=>'',
                "pri"=>'11110',
				"id"=>$id,
            ];
            $dialog_html=$widget->showEditWidget($urlconfig);
            $list_html=$widget->showListWidget($model->name,$urlconfig,$dialog_html);
            return $this->View("index")->with(
                [
                    "table_widget"=>$list_html,
                ]
            );
        }
    }

    public function getView(Request $req)
    {
		if(!$req->ajax()){
			//return;
		}
        $id=$req->get('id',0);
		if($id==0){
			$re['action']='add';
			$re['_token']=csrf_token();
			return json_encode($re);
		}
		else{
            $user=new User();
            $widgets=new UserWidget();
            $re=$user->getUserData($id);
            if(!$re){
			    $re['action']='add';
            }
            else{
			    $re['action']='edit';
            }
            //$widgets->translateToView($re);
			$re['_token']=csrf_token();
			return json_encode($re);
		}
    }

    public function postModify(Request $req)
    {
        $id=$req->get("id",0);
        $action=$req->get('action');
        $roleids=$this->getValuesByPrefix("role_");
        $data=Array('name'=>$req->get('name'),'role'=>implode(",",$roleids),"status"=>$req->get('status'));
        switch($action){
            case "add":
            $user=new User();
            $data['password']=$req->get('password');
            $data['email']=$req->get('email');
            $user=new User();
            $result=$user->modifyUser(0,$data);
            return json_encode($result);
            break;

            case "edit":
            if($id == 0){
                $result=['code'=>0,'msg'=>'操作停留超时请重新打开'];
                return json_encode($result);
            }
            if($req->get('password')){
                $data['password']=$req->get('password');
            }
            $user=new User();
            $result=$user->modifyUser($id,$data);
            return json_encode($result);
            break;
        }
    }

    public function getDelete(Request $req)
    {
        $id=$req->get('id');
        $cat=new User();
        $result=$cat->deleteById($id);
        if($result){
            return json_encode(Array('code'=>1));
        }
        return json_encode(Array('code'=>0,'msg'=>'删除失败'));
    }
}
