<?php
namespace App\Http\Controllers\Admin;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\StatDefine;
use App\Models\ContentTable;
use App\Http\Requests\MenuModify;
use App\Http\Controllers\Admin\Widgets\StatWidget;

class StatController extends AdminController
{
    public function index(Request $req)
	{
		$id=$req->get("id",0);
        $cate=new StatDefine();
        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
            $result=$cate->stats($start,$length,$id);
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
            $models=new DataTable();
            $this->breadcrumb=$cate->getPath($id?$id:StatDefine::STAT_ID);
            foreach($this->breadcrumb as &$row){
                $row['url']=$this->getUrl()."?id=".$row['id'];
            }
            $stat_widget=new StatWidget();
            $urlconfig=[
                "url"=>$this->getUrl()."?id=$id",
                "edit_url"=>$this->getUrl("modify"),
                "view_url"=>$this->getUrl("view"),
                "open_url"=>$this->geturl(""),
                "delete_url"=>$this->getUrl("delete"),
                "field_url"=>$this->getUrl("field"),
                "pri"=>'11110',
				"id"=>$id,
            ];

            $dialog_html=$stat_widget->showEditWidget($urlconfig);
            $list_html=$stat_widget->showListWidget("统计管理",$urlconfig,$dialog_html);
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
            $stat=new StatDefine();
            $re=$stat->getDataById($id);
            $widgets=new StatWidget();
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
            $widget=new StatWidget();
            $data=Array('name'=>$req->get('name'),'note'=>$req->get('note'),'tablename'=>$req->get('tablename'),'condition'=>$req->get('condition'),'item'=>$req->get('item'));
            $widget->tranformSetting($data,true);
            $stat=new StatDefine();
            $result=$stat->addStat($id,$data);
            return json_encode($result);
            break;

            case "edit":
            $widget=new StatWidget();
            $data=Array('name'=>$req->get('name'),'note'=>$req->get('note'),'tablename'=>$req->get('tablename'),'condition'=>$req->get('condition'),'item'=>$req->get('item'));
            $widget->tranformSetting($data,true);
            $stat=new StatDefine();
            $result=$stat->editStat($id,$data);
            return json_encode($result);
            break;
        }
    }

    public function getDelete(Request $req)
    {
        $id=$req->get('id');
        $stat=new StatDefine();
        $result=$stat->deleteStat($id);
        if($result){
            return json_encode(Array('code'=>1));
        }
        return json_encode(Array('code'=>0,'msg'=>'删除失败'));
    }

    public function getDetail(Request $req)
    {
        $statid=intval($req->get('statid'));
        if(!$statid){
        }
        $stat=new StatDefine();
        $data=$stat->getDataById($statid);
        $widget=new StatWidget();
        $widget->tranformSetting($data,false);
        $content=new ContentTable();
        $month=$req->get('month');
        $rows=$content->getStatData($data['condition'],$data['item'],$data['tablename'],$month);
        $max_day[]=0;
        $max_month[]=0;
        foreach($rows['header'] as $k=>$v){
            if(!isset($max_day[$k])){
                $max_day[$k]=0;
            }
            foreach($rows['days'] as $row){
                if($row->{$k} > $max_day[$k]){
                    $max_day[$k]=$row->{$k};
                }
            }

            if(!isset($max_month[$k])){
                $max_month[$k]=0;
            }
            foreach($rows['months'] as $row){
                if($row->{$k} > $max_month[$k]){
                    $max_month[$k]=$row->{$k};
                }
            }
        }
        return $this->View("detail")
            ->with('max_day',$max_day)
            ->with('max_month',$max_month)
            ->with('header',$rows['header'])
            ->with('days',$rows['days'])
            ->with('stat_name',$data['name'])
            ->with('curl',$req->url()."?statid=$statid")
            ->with('months',$rows['months']);
    }
}
