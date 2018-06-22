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
}
