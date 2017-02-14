<?php
namespace App\Http\Controllers;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\DataTable;
use App\Models\ConstDefine;
use App\Http\Requests\MenuModify;
use App\Http\Controllers\Widgets\DataTableWidget;

class ModelController extends Controller
{
    public function index(Request $req)
	{
        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $dt=new DataTable();
            $result=$dt->tables($start,$length);
            (new DataTableWidget())->translateData($result['data']);
            $data=Array(
                "draw"=>$draw,
                "recordsTotal"=>$result['total'],
                "recordsFiltered"=>$result['total'],
                "data"=>$result['data'],
            );

            return json_encode($data);
        }
        else{
            $table_widget=new DataTableWidget();

            $urlconfig=[
                "url"=>$this->getUrl(),
                "edit_url"=>$this->getUrl("modify"),
                "view_url"=>$this->getUrl("view"),
                "delete_url"=>$this->getUrl("delete"),
                "field_url"=>$this->getUrl("field"),
                "pri"=>'11111',
            ];
            $dialog_html=$table_widget->showModelEditWidget($urlconfig);
            $list_html=$table_widget->showModelListWidget("模型列表",$urlconfig,$dialog_html);

            return $this->View("models")->with(
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
            $table_widget=new DataTableWidget();
            $re=$table_widget->getDefaultModelDefine();
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

    public function getField(Request $req)
    {
        $id=$req->get('id');
        if($id<1){
            //Redirect::to($this->getUrl());
        }
        if($req->ajax()){

            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $dt=new DataTable();
            $result=$dt->fields($id,$start,$length);
            $widget=new DataTableWidget();
            $widget->translateData($result['data']);
            $data=Array(
                "draw"=>$draw,
                "recordsTotal"=>$result['total'],
                "recordsFiltered"=>$result['total'],
                "data"=>$result['data'],
            );

            return json_encode($data);
        }
        else{
            $table_widget=new DataTableWidget();
            $urlconfig=[
                "url"=>$this->getUrl('field')."?id=".$id,
                "edit_url"=>$this->getUrl("modifyfield"),
                "view_url"=>$this->getUrl("viewfield"),
                "delete_url"=>$this->getUrl("deletefield"),
                "field_url"=>'',
                "pri"=>'11110',
            ];
            $dt=new DataTable();
            $tableinfo=$dt->getDataById($id);
            $dialog_html=$table_widget->showFieldEditWidget(Array("const_url"=>$this->getUrl("consts"),'modelid'=>$id));
            $list_html=$table_widget->showFieldListWidget(sprintf("模型-%s[%s]",$tableinfo['note'],$tableinfo['name']),$urlconfig,$dialog_html);
            return $this->View("models")->with(
                [
                    "table_widget"=>$list_html,
                ]
            );
        }
    }
    /**
     * [postModifyField description]
     * @method postModifyField
     * @param  Request         $req [description]
     * @return [type]               [description]
     */
    public function postModifyField(Request $req)
    {
        $modelid=$req->get('modelid');
        $action=$req->get('action');
        if($modelid<1){
            return;
        }
        switch($action){
            case "add":
            $fieldwidget=new DataTableWidget();
            $allinput=$req->all();
            $setdata=$fieldwidget->tranformFieldSetting($allinput,true);
            $data=Array(
                'note'=>$req->get("note"),
                'name'=>$req->get("name"),
                'type'=>$req->get("type"),
                'setting'=>$setdata["setting"],
                'order'=>0,
            );
            $dtmodel=new DataTable();
            $result=$dtmodel->addField($modelid,$setdata['type'],$data);
            return json_encode($result);
            break;

            case "edit":
            $id=$req->get('id');
            $note=$req->get('note');
            $setting=$req->get('setting');
            $name=$req->get("name");
            $allinput=$req->all();
            $fieldwidget=new DataTableWidget();
            $setdata=$fieldwidget->tranformFieldSetting($allinput,true);
            $dtmodel=new DataTable();
            $result=$dtmodel->editField($id,Array('name'=>$name,'note'=>$note,'type'=>$setdata['type'],'setting'=>$setdata["setting"]));
            return json_encode($result);
            break;
        }
    }

    public function getViewField(Request $req)
    {
        $id=$req->get('id');
        if($id==0){
            $table_widget=new DataTableWidget();
            $re=$table_widget->getDefaultFieldDefine();
            $re['action']='add';
            $re['_token']=csrf_token();
            return json_encode($re);
        }
        else{
            $tb=new DataTable();
            $re=$tb->getDataById($id);
            $table_widget=new DataTableWidget();
            $table_widget->tranformFieldSetting($re,false);
            if($table_widget->isConstField($re)){
                $data=$tb->getDataById($re['const']);
                $re['const_parentid']=$data['parentid'];
            }

            $re['action']='edit';
            $re['_token']=csrf_token();
            return json_encode($re);
        }
    }

    public function getDeleteField(Request $req)
    {
        $id=$req->get('id');
        $dtmodel=new DataTable();
        $result=$dtmodel->deleteField($id);
        if($result){
            return json_encode(Array('code'=>1));
        }
        return json_encode(Array('code'=>0,'msg'=>'删除失败'));
    }

    public function getConsts(Request $req)
    {
        $id=$req->get('id');
        if($id<1){
            //Redirect::to($this->getUrl());
        }
        if($req->ajax()||true){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $cd=new ConstDefine();
            $result=$cd->consts($id,$start,$length);

            $data=Array(
                "draw"=>$draw,
                "recordsTotal"=>$result['total'],
                "recordsFiltered"=>$result['total'],
                "data"=>$result['data'],
            );
            $curdata=$cd->getDataById($id);
            if($curdata){
                $data['parentname']=$curdata["name"];
            }
            return json_encode($data);
        }

    }

}
