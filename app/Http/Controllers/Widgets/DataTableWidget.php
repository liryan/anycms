<?php

namespace App\Http\Controllers\Widgets;

use App\Models\DataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
class DataTableWidget extends Controller
{
	private static $DIALOG_ID=1;
	//模型表单字段
	private $model_fields=[
		['name'=>'id','label'=>'编号','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'number'],
		['name'=>'note','label'=>'模型名字','place_holder'=>'请输入模型名(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'name','label'=>'表名','place_holder'=>'请输入数据表名(字母)','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'setting','label'=>'备注','place_holder'=>'请输入备注','default'=>'','editable'=>true,'listable'=>false,'type'=>'html'],
		['name'=>'created_at','label'=>'创建日期','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'datetime'],
		['name'=>'_internal_field','label'=>'操作','place_holder'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>'text']
	];
	//模型字段的属性定义
	private $fields_it=[
		['name'=>'id','label'=>'编号','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'number'],
		['name'=>'note','label'=>'字段名字','place_holder'=>'','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'name','label'=>'表字段','place_holder'=>'','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'type','label'=>'类型','place_holder'=>'','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'created_at','label'=>'创建日期','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'datetime'],
		['name'=>'_internal_field','label'=>'操作','place_holder'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>'text']
	];
	//字段的扩展属性定义
	private $field_setting=[
		["name"=>"tablename",'label'=>'关联表名','default'=>''],
		["name"=>"tablefield",'label'=>'关联表字段','default'=>''],
		["name"=>"listable",'label'=>'可列表','default'=>'0'],
		["name"=>"default",'label'=>'缺省值','default'=>''],
		['name'=>'constid','label'=>'选择的常量ID','default'=>'']
	];
	/**
	 * [showModelListWidget description]
	 * @method showModelListWidget
	 * @param  [type]              $name            [description]
	 * @param  [type]              $urlconfig       ['url','edit_url','view_url','delete_url','field_url']
	 * @param  string              $new_dialog_html [description]
	 * @return [type]                               [description]
	 */
	public function showModelListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=View::make("widgets.DataTable");
		$fields=[];
		foreach($this->model_fields as $row){
			if($row['listable']){
				$fields[]=$row;
			}
		}
		$view->with("fields",$fields);
		$view->with("name",$name);
		$view->with($urlconfig);
		return $view->with('new_dialog',$new_dialog_html)->render();
	}

	public function showModelEditWidget($urlconfig)
	{
		$view=View::make("widgets.DataEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach($this->model_fields as $row){
			if($row['editable']){
				$fields[]=$row;
			}
		}
		return $view->with(['inputs'=>$fields,'dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function getDefaultModelDefine()
	{
		$data=[];
		foreach($this->model_fields as $row){
			if($row['editable']){
				$data[$row['name']]=$row['default'];
			}
		}
		return $data;
	}

	public function showFieldEditWidget($urlconfig)
	{
		$view=View::make("widgets.FieldEdit");
		$view->with($urlconfig);
		return $view->with(['dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function showFieldListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=View::make("widgets.DataTable");
		$fields=[];
		foreach($this->fields_it as $row){
			if($row['listable']){
				$fields[]=$row;
			}
		}
		$view->with("fields",$fields);
		$view->with("name",$name);
		$view->with($urlconfig);
		return $view->with('new_dialog',$new_dialog_html)->render();
	}
	/**
	 * [把字段的setting部分解析或者合成]
	 * @method tranformFieldSetting
	 * @param  [type]               $data [要过滤的数据]
	 * @param  [type]               $in   [true:表示把字段合成setting，false表示解析setting]
	 * @return [type]                     [description]
	 */
	public function tranformFieldSetting(&$data,$in)
	{
		if($in){
			$setting=[];
			foreach($this->field_setting as $row){
				$setting[$row['name']]=$data[$row['name']];
			}
			$data['setting']=json_encode($setting);
		}
		else{

			$obj=json_decode($data['setting'],true);
			if(is_array($obj)){
				foreach($obj as $k=>$v){
					$data[$k]=>$v;
				}
			}
		}
	}

	public function getDefaultFieldDefine()
	{
		$data=[];
		foreach($this->field_setting as $row){
			$data[$row['name']]=$row['default'];
		}
		return $data;
	}

}
