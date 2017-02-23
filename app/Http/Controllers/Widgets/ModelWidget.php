<?php

namespace App\Http\Controllers\Widgets;

use App\Models\DataTable;
use Illuminate\Support\Facades\View;

class ModelWidget extends Widget
{
	private static $DIALOG_ID=1;
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
		$view=$this->getDataTableView();
		$fields=[];
		foreach(DataTable::$model_fields as $row){
			if($row['listable']){
				$fields[]=$row;
			}
		}
		$view->with("fields",$fields);
		$view->with("name",$name);
		$view->with($urlconfig);
		$view->with('search_widget','');
		return $view->with('new_dialog',$new_dialog_html)->render();
	}

	public function showModelEditWidget($urlconfig)
	{
		$view=$this->getView("DataEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach(DataTable::$model_fields as $row){
			if($row['editable']){
				$fields[]=$row;
			}
		}
		return $view->with(['inputs'=>$fields,'dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function getDefaultModelDefine()
	{
		$data=[];
		foreach(DataTable::$model_fields as $row){
			if($row['editable']){
				$data[$row['name']]=$row['default'];
			}
		}
		return $data;
	}
	public function isConstField($data)
	{
		if($data['type']==5){
			return true;
		}
		return false;
	}
	public function showFieldEditWidget($urlconfig)
	{
		$view=$this->getView("FieldEdit");
		$view->with('types',DataTable::$field_type);
		$view->with($urlconfig);
		return $view->with(['dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function showFieldListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=$this->getDataTableView();
		$fields=[];
		foreach(DataTable::$fields_it as $row){
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
			foreach(DataTable::$field_setting as $row){
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
			foreach(DataTable::$field_type as $row){
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
						else{
							$data[$k]=$v;
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
		foreach(DataTable::$field_setting as $row){
			$data[$row['name']]=$row['default'];
		}
		return $data;
	}
	public function translateData(&$data)
	{
		foreach($data as &$row){
			foreach(DataTable::$field_type as $define){
				if($row['type']==$define['value']){
					$row['type']=$define['name'];
				}
			}
			$row['_internal_field']='';
		}
	}
}
