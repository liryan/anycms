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
		['name'=>'const','label'=>'选择的常量ID','default'=>''],
		['name'=>'size','label'=>'字段大小','default'=>'']
	];
	//字段的类型映射
	private $field_type=[

	];
	public function __construct(){
		$this->field_type=[
			['name'=>'整数字','value'=>'1','type'=>'integer','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("integer(%d) default %d",$data['size'],$data['default']);
				return sprintf("integer(%d)",$data['size']?$data['size']:11);
			}],
			['name'=>'文本','value'=>'2','type'=>'varchar','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
				return sprintf("varchar(%d)",$data['size']?$data['size']:255);
			}],
			['name'=>'编辑器','value'=>'3','type'=>'text','DBdefine'=>function($data){
				return "text";
			}],
			['name'=>'日期','value'=>'4','type'=>'datetime','DBdefine'=>function($data){
				return $data['size']==1?'datetime':'date';
			}],
			['name'=>'选择列表','value'=>'5','type'=>'integer','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("integer(11) default %d",$data['default']);
				return sprintf("integer(11)");
			}],
			['name'=>'多选列表','value'=>'6','type'=>'varchar','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
				return sprintf("varchar(%d)",$data['size']?$data['size']:255);
			}],
			['name'=>'图片','value'=>'7','type'=>'varchar','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
				return sprintf("varchar(%d)",$data['size']?$data['size']:255);
			}],
			['name'=>'小数点数字','value'=>'8','type'=>'number','DBdefine'=>function($data){
				if(strlen($data['default'])>0)
					return sprintf("decimal(%d,%d) default %.02f",$data['size'],$data['size_bit'],$data['default']);
				return sprintf("decimal(%d,%d)",$data['size'],$data['size_bit']);
			}],
		];
	}
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
		$view->with('types',$this->field_type);
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
		$result=[];
		if($in){
			$setting=[];
			foreach($this->field_setting as $row){
				if(!isset($data[$row['name']])){
					$data[$row['name']]='';
				}
				$setting[$row['name']]=$data[$row['name']];
				if($row['name']=='size'){
					if(isset($data['size_bit'])){
						$setting[$row['name']].=".".$data['size_bit'];
					}
				}
			}
			$result['setting']=json_encode($setting);
			foreach($this->field_type as $row){
				if($row['value']==$data['type']){
					$result['type']=$row['DBdefine']($data);
				}
			}
		}
		else{
			$obj=json_decode($data['setting'],true);
			if(is_array($obj)){
				foreach($obj as $k=>$v){
					if($k=='size'){
						$ar=explode(".",$v);
						if(sizeof($ar)==2){
							$data['size']=$ar[0];
							$data['size_bit']=$ar[1];
						}
					}
					else{
						$data[$k]=$v;
					}
				}
			}
		}
		return $result;
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
