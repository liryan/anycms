<?php
namespace App\Http\Controllers;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Category;
use App\Models\ContentTable;
use App\Http\Controllers\Widgets\ContentWidget;
class ContentController extends AdminController
{
	public function index(Request $req)
	{
		$id=$req->get("id",0);
		$cate=new Category();
		$dt=new DataTable();
		$modelid=$cate->getModelId($id);
		$table_define=$dt->tableColumns($modelid);

        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
			$widget=new ContentWidget($table_define);
			$search_clouser=$widget->generateSearchClouser($id,$req->all());
			$content=new ContentTable();
            $result=$content->getList($start,$length,$table_define,$id,$search_clouser);
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
            $this->breadcrumb=$cate->getPath($id?$id:Category::CATEGORY_ID);
            foreach($this->breadcrumb as &$row){
                $row['url']=$this->getUrl()."?id=".$row['id'];
            }
            $widget=new ContentWidget($table_define);
			$search_clouser=$widget->generateSearchClouser($id,$req->all());
            $urlconfig=[
                "url"=>$this->getUrl()."?id=$id",
                "edit_url"=>$this->getUrl("modify"),
                "view_url"=>$this->getUrl("view"),
                "open_url"=>$this->geturl(""),
                "delete_url"=>$this->getUrl("delete"),
                "field_url"=>'',
                "pri"=>'11110',
				"id"=>$id,
            ];

            $dialog_html=$widget->showEditWidget($urlconfig);
            $list_html=$widget->showListWidget($table_define['note'],$urlconfig,$dialog_html,$table_define);
            return $this->View("index")->with(
                [
                    "table_widget"=>$list_html,
                ]
            );
        }
	}
}
