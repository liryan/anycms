<?php
namespace App\Http\Controllers\Admin;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Category;
use App\Models\ContentTable;
use App\Http\Controllers\Admin\Widgets\ContentWidget;
use EasyThumb\EasyThumb;
use Exception;
class ContentController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
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
				$row->_internal_field='';
			}
            $widget->translateData($result['data']); //常量更改成名称 
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
                "edit_url"=>$this->getUrl("modify")."?catid=$id",
                "view_url"=>$this->getUrl("view")."?catid=$id",
                "open_url"=>$this->geturl("")."?catid=$id",
                "delete_url"=>$this->getUrl("delete")."?catid=$id",
                "field_url"=>'',
                "pri"=>'11110',
				"id"=>$id,
            ];
            $dialog_html=$widget->showEditWidget($urlconfig);
            $list_html=$widget->showListWidget($table_define['info']['note'],$urlconfig,$dialog_html);
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
			return;
		}
        $id=$req->get('id',0);
		$catid=$req->get("catid",0);
		$cate=new Category();
		$dt=new DataTable();
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);
		if($id==0){
			$re['action']='add';
			$re['_token']=csrf_token();
			return json_encode($re);
		}
		else{
			$content=new ContentTable();
            $re=$content->getContent($table_define,$id);
            $widgets=new ContentWidget($table_define);
            $widgets->translateToView($re);
			$re['action']='edit';
			$re['_token']=csrf_token();
			return json_encode($re);
		}
	}

    public function postModify(Request $req)
    {
		$catid=$req->get("catid",0);
		$cate=new Category();
		$dt=new DataTable();
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);
        $act=$req->get('action');
        $data=$this->fillRowWithPost($table_define['columns'],$req);
        $dt->fillDefault($data);
        $data['category']=$catid;
        $result=0;
        $re=['code'=>0,'msg'=>'未知的操作'];
        switch($act){
        case 'edit':
            $dt->filterForEdit($data);
            $content=new ContentTable();
            $contentid=$req->get('id');
            $result=$content->editContent($table_define,$contentid,$data);
            $re=['code'=>$result?1:0,'msg'=>$result?"成功修改数据":"修改失败了"];
            break;
        case 'add':
            $content=new ContentTable();
            $result=$content->addContent($table_define,$data);
            $re=['code'=>$result?1:0,'msg'=>$result?"成功增加数据":"增加失败了"];
            break;
        }
		return json_encode($re);
    }

    private function fillRowWithPost($define,Request $req)
    {
        $data=[];
        foreach($define as $row){
            $data[$row['name']]=$req->get($row['name']);
            if(is_array($data[$row['name']])){
                $data[$row['name']]=trim(implode(",",$data[$row['name']]));
            }
        }
        return $data;
    }

	public function postUploadImage(Request $req)
	{
        try{
            $fieldname=array_keys($_FILES);
            if(!$fieldname){
                return '';
            }
            $fieldname=$fieldname[0];
            $root_path=public_path();
            $sub_path="/upimages";
            $path=EasyThumb::upload($fieldname)->where($root_path.$sub_path)->autodir()->limit(1000*1000,EasyThumb::PNG|EasyThumb::GIF|EasyThumb::JPG)->size(100,100,EasyThumb::SCALE_FREE)->done();
            $info=pathinfo($path['origin']);
            $result=[
                'success'=>'ok',
                'url'=>str_replace($root_path,'',$path['origin']),
                'id'=>$fieldname,
                'result'=>'SUCCESS'
            ];
            return json_encode($result);

        }
        catch(Exception $e){
            $result=[
                'originalName'=>$_FILES[$fieldname]['name'],
                'name'=>'',
                'url'=>'',
                'size'=>0,
                'type'=>'',
                'state'=>$e->getMessage(),
            ];
            return json_encode($result);
        }
	}
	/**
	 * postUploadfile 
     * 百度图片上传接口
	 * success: {"originalName":"timg.jpeg","name":"1494575499954.jpeg","url":"upload\/20170512\/1494575499954.jpeg","size":121080,"type":".jpeg","state":"SUCCESS"}
     * failed : {"originalName":"install","name":null,"url":null,"size":15,"type":"","state":"错误消息"}
	 * @param Request $req 
	 * @access public
	 * @return void
	 */
	public function postUploadfile(Request $req)
	{
        try{
            $root_path=public_path();
            $sub_path="/upimages";
            $path=EasyThumb::upload("upfile")->where($root_path.$sub_path)->autodir(EasyThumb::SHA1_DIR)->limit(1000*1000,EasyThumb::PNG|EasyThumb::GIF|EasyThumb::JPG)->size(100,100,EasyThumb::SCALE_FREE)->done();
            $info=pathinfo($path['origin']);
            $result=[
                'originalName'=>$_FILES['upfile']['name'],
                'name'=>$info['filename'],
                'url'=>str_replace($root_path,'',$path['origin']),
                'size'=>filesize($path['origin']),
                'type'=>$info['extension'],
                'state'=>'SUCCESS'
            ];
            return json_encode($result);

        }
        catch(Exception $e){
            $result=[
                'originalName'=>$_FILES['upfile']['name'],
                'name'=>'',
                'url'=>'',
                'size'=>0,
                'type'=>'',
                'state'=>$e->getMessage(),
            ];
            return json_encode($result);
        }
	}

	public function postDeletefile(Request $req)
	{
        return json_encode(Array('result'=>'success'));
	}

    public function getDelete(Request $req)
    {
		$cate=new Category();
		$dt=new DataTable();
        $catid=$req->get("catid");
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);

        $content=new ContentTable();   
        $id=$req->get("id");
        $result=$content->deleteContent($table_define,$id);
        $re=['code'=>$result?1:0,'msg'=>$result?'success':'failed'];
        return json_encode($re);
    }
}