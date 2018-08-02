<?php
namespace App\Http\Controllers\Admin;

use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Category;
use App\Models\Privileges;
use App\Models\ContentTable;
use App\Http\Controllers\Admin\Widgets\ContentWidget;
use EasyThumb\EasyThumb;
use Exception;
class ContentController extends AdminController
{
    private $pridb;
    public function __construct()
    {
        parent::__construct();
        $this->pridb=new Privileges();
    }

	public function index(Request $req)
	{
		$catid=$req->get("catid",0);
		$cate=new Category();
		$dt=new DataTable();
        $modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);
        if($req->ajax()){
            $start=$req->get('start');
            $length=$req->get('length');
            $draw=intval($req->get('draw'));
			$widget=new ContentWidget($table_define);
			$search_clouser=$widget->generateSearchClouser($catid,$req->all());
			$content=new ContentTable();
            $result=$content->getList($start,$length,$table_define,$catid,$search_clouser);
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
            $info=$cate->getDatabyId($modelid);
            $model_setting=json_decode($info['setting'],true);
            $this->breadcrumb=$cate->getPath($catid?$catid:Category::CATEGORY_ID);
            foreach($this->breadcrumb as &$row){
                $row['url']=$this->getUrl()."?catid=".$row['id'];
            }
            $widget=new ContentWidget($table_define);
			$search_clouser=$widget->generateSearchClouser($catid,$req->all());
            $urlconfig=[
                "model_url"=>isset($model_setting['url'])?$model_setting['url']:'',
                "url"=>$this->getUrl()."?catid=$catid",
                "edit_url"=>$this->getUrl("modify")."?catid=$catid",
                "view_url"=>$this->getUrl("view")."?catid=$catid",
                "preview_url"=>$this->getUrl("preview")."?catid=$catid",
                "open_url"=>$this->geturl("")."?catid=$catid",
                "delete_url"=>$this->getUrl("delete")."?catid=$catid",
                "field_url"=>'',
                "pri"=>$this->pridb->getContentPri($modelid,$req->session()->get('admin')),
				"catid"=>$catid,
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

    public function getPreview(Request $req)
    {
        /*
		if(!$req->ajax()){
			return;
		}
         */

        $id=$req->get('id',0);
		$catid=$req->get("catid",0);
		$cate=new Category();
		$dt=new DataTable();
		$modelid=$cate->getModelId($catid);

        if(!$this->pridb->checkPri($modelid,Privileges::VIEW,$req->session()->get('admin'))){
            return $this->error($req,'权限不足');
        }

		$table_define=$dt->tableColumns($modelid);
		if($id==0){
            return '';
		}
		else{
			$content=new ContentTable();
            $re=$content->getContent($table_define,$id);
            $widgets=new ContentWidget($table_define);
            return $widgets->showViewWidget($re);
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

        if(!$this->pridb->checkPri($modelid,Privileges::VIEW,$req->session()->get('admin'))){
            return $this->error($req,'权限不足');
        }

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
			$re['action']='edit';
			$re['_token']=csrf_token();
            $data=[json_decode(json_encode($re))];
            $widgets->translateData($data,true);
            $widgets->translateField($data[0]);
			return json_encode($data[0],true);
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
        $re=['code'=>0,'msg'=>'没有数据可以修改'];
        if(!$data){
		    return json_encode($re);
        }
        $data['category']=$catid;
        $result=0;
        $pridb=new Privileges();
        switch($act){
        case 'edit':
            $dt->fillDefault($data,false);
            if(!$this->pridb->checkPri($modelid,Privileges::EDIT,$req->session()->get('admin'))){
                return $this->error($req,'权限不足');
            }
            $dt->filterForEdit($data);
            $content=new ContentTable();
            $contentid=$req->get('id');
            $result=$content->editContent($table_define,$contentid,$data,$catid);
            $re=['code'=>$result?1:0,'msg'=>$result?"成功修改数据":"修改失败了"];
            break;
        case 'add':
            $dt->fillDefault($data,true);
            if(!$this->pridb->checkPri($modelid,Privileges::ADD,$req->session()->get('admin'))){
                return $this->error($req,'权限不足');
            }
            $content=new ContentTable();
            $result=$content->addContent($table_define,$data);
            $re=['code'=>$result?1:0,'msg'=>$result?"成功增加数据":"增加失败了"];
            break;
        }
		return json_encode($re);
    }

    /**
     * fillRowWithPost 
     * 处理提交的数据
     * @param mixed $define 
     * @param Request $req 
     * @access private
     * @return void
     */
    private function fillRowWithPost($define,Request $req)
    {
        $data=[];
        foreach($define as $row){
            if(!$row['editable']){
                continue;
            }
            $data[$row['name']]=$req->get($row['name']);
            if($data[$row['name']] && $data[$row['name']][0]=='['){
                $data[$row['name']]=json_decode($data[$row['name']],true);
            }
            if(is_array($data[$row['name']])){
                $data[$row['name']]=trim(implode(",",$data[$row['name']]));
            }
            if($row['type'] == DataTable::DEF_MULTI_LIST){
                $values=$this->getValuesByPrefix($row['name']."_",true);
                $data[$row['name']]=implode(",",$values);
                $this->removeValueByPrefix($data,$row['name']."_");
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
            $path=EasyThumb::upload($fieldname)->where($root_path.$sub_path)->autodir(EasyThumb::SHA1_DIR)->limit(1000*1000,EasyThumb::PNG|EasyThumb::GIF|EasyThumb::JPG)->size(100,100,EasyThumb::SCALE_FREE)->done();
            $info=pathinfo($path['origin']);
            $result=[
                'success'=>'ok',
                'url'=>str_replace($root_path,'',$path['origin']),
                'id'=>$fieldname,
                'result'=>'SUCCESS',
                '_token'=>csrf_token()
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
                '_token'=>csrf_token(),
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
            $path=EasyThumb::upload("upfile")->where($root_path.$sub_path)->autodir()->limit(1000*1000,EasyThumb::PNG|EasyThumb::GIF|EasyThumb::JPG)->size(100,100,EasyThumb::SCALE_FREE)->done();
            $info=pathinfo($path['origin']);
            $result=[
                'originalName'=>$_FILES['upfile']['name'],
                'name'=>$info['filename'],
                'url'=>str_replace($root_path,'',$path['origin']),
                'size'=>filesize($path['origin']),
                'type'=>$info['extension'],
                'state'=>'SUCCESS',
                '_token'=>csrf_token(),
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
                '_token'=>csrf_token(),
                'state'=>$e->getMessage(),
            ];
            return json_encode($result);
        }
	}

	public function postDeletefile(Request $req)
	{
        return json_encode(Array('error'=>'','result'=>'success'));
	}

    public function getDelete(Request $req)
    {
		$cate=new Category();
		$dt=new DataTable();
        $catid=$req->get("catid");
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);

        if(!$this->pridb->checkPri($modelid,Privileges::DELETE,$req->session()->get('admin'))){
            return $this->error($req,'权限不足');
        }

        $content=new ContentTable();   
        $id=$req->get("id");
        $result=$content->deleteContent($table_define,$id,$catid);
        $re=['code'=>$result?1:0,'msg'=>$result?'success':'failed'];
        return json_encode($re);
    }

    public function postBatchdel(Request $req)
    {
		$cate=new Category();
		$dt=new DataTable();
        $catid=$req->get("catid");
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);

        if(!$this->pridb->checkPri($modelid,Privileges::DELETE,$req->session()->get('admin'))){
            return $this->error($req,'权限不足');
        }
        $cond=explode("-",$req->get("ids")); 
        $content=new ContentTable();   
        $result=$content->deleteContentBatch($table_define,$cond,$catid);
        return $this->ajax($result?1:0,'');
    }

    public function postBatchedit(Request $req)
    {
        $catid=$req->get("catid");
        $cond=explode("-",$req->get("ids")); 
        $name=explode("|",$req->get('name'));
        $value=$req->get('value');

		$cate=new Category();
		$dt=new DataTable();
        $catid=$req->get("catid");
		$modelid=$cate->getModelId($catid);
		$table_define=$dt->tableColumns($modelid);

        if(!$this->pridb->checkPri($modelid,Privileges::EDIT,$req->session()->get('admin'))){
            return $this->error($req,'权限不足');
        }

        $data=Array($name[0]=>$value);
        $content=new ContentTable();
        $result=$content->editContentBatch($table_define,$cond,$data,$catid);
        return $this->ajax($result?1:0,'');
    }
}
