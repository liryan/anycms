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
        $data=Array('name'=>$req->get('name'),'note'=>$req->get('note'),'tablename'=>$req->get('tablename'),'condition'=>$req->get('condition'),'item'=>$req->get('item'),'group_date'=>$req->get('group_date',0),'index'=>$req->get('index'));
        switch($action){
            case "add":
            $widget=new StatWidget();
            $widget->tranformSetting($data,true);
            $stat=new StatDefine();
            $result=$stat->addStat($id,$data);
            return json_encode($result);
            break;

            case "edit":
            $widget=new StatWidget();
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
        $page=intval($req->get('page'));
        $item=intval($req->get('index'));
        if(!$statid){
        }
        
        $stat=new StatDefine();
        $data=$stat->getDataById($statid);
        $widget=new StatWidget();
        $widget->tranformSetting($data,false);
        $content=new ContentTable();
        $month=$req->get('month');
        $rows=$content->getStatData($data['condition'],$data['item'],$data['tablename'],$data['index'],$data['group_date'],$page,$item,$month);
       
        $max_day=[];
        $max_month=[];
        $day_total=[];
        $month_total=[];
        $days=[];
        $emptyObj=new \StdClass();
        foreach($rows['header'] as $k=>$v){
            $emptyObj->{$k}=0; 
        }
        
        if($data['group_date']){
            foreach($rows['header'] as $k=>$v){
                if(!isset($max_day[$k])){
                    $max_day[$k]=0;
                }
                foreach($rows['days'] as $row){
                    if($row->{$k} > $max_day[$k]){
                        $max_day[$k]=$row->{$k};
                    }
                    $day_total[$k]=(isset($day_total[$k])?$day_total[$k]:0)+$row->{$k};
                }
    
                if(!isset($max_month[$k])){
                    $max_month[$k]=0;
                }
                foreach($rows['months'] as $row){
                    if($row->{$k} > $max_month[$k]){
                        $max_month[$k]=$row->{$k};
                    }
                    $month_total[$k]=(isset($month_total[$k])?$month_total[$k]:0)+$row->{$k};
                }
            }
            if(!$month){
                $start=date('Ym')."01";
            }
            else{
                $start=$month."01";
            }

            for($s=0;$s<31;$s++){
                $tmp= clone $emptyObj;
                $tmp->stat_day=$start+$s;
                $days[$tmp->stat_day]=$tmp;
            }
    
            foreach($rows['days'] as $r){
                $days[$r->stat_day]=$r;
            }
    
            if($rows['index_data']){
                foreach($rows['index_data'] as $r){
                    if($r->key==$item){
                        $data['name']=$r->name;
                        break;
                    }
                }
            }
        }
        return $this->View("detail")
            ->with('page',$page)
            ->with('index_data',$rows['index_data'])
            ->with('index',$item)
            ->with('max_day',$max_day)
            ->with('day_total',$day_total)
            ->with('month_total',$month_total)
            ->with('max_month',$max_month)
            ->with('header',$rows['header'])
            ->with('days',array_values($days))
            ->with('stat_name',$data['name'])
            ->with('curl',$req->url()."?statid=$statid")
            ->with('others',$rows['others'])
            ->with('months',$rows['months']);
    }
}
