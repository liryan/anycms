<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Models\DataTable;
use App\Models\Category;
use Illuminate\Support\Facades\View;

class CategoryWidget extends Widget
{
	private static $DIALOG_ID=1;
	public function showListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=$this->getDataTableView();
		$fields=[];
		foreach(Category::$cate_fields as $row){
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

	public function showEditWidget($urlconfig)
	{
		$view=$this->getView("CategoryEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach(Category::$cate_fields as $row){
			if($row['editable']){
				$fields[]=$row;
			}
		}
		return $view->with(['inputs'=>$fields,'dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function tranformSetting(&$data,$in)
	{
		$result=[];
		if($in){
			$setting=[];
			foreach(Category::$field_setting as $row){
				if(!isset($data[$row['name']])){
					$data[$row['name']]=$row['default'];
				}
                else{
				    $setting[$row['name']]=$data[$row['name']];
                }
			}
			$result['setting']=json_encode($setting);
			$data['setting']=$result['setting'];
		}
		else{
			$obj=json_decode($data['setting'],true);
			if(is_array($obj)){
				foreach($obj as $k=>$v){
					$data[$k]=$v;
				}
			    foreach(Category::$field_setting as $row){
                    if(!isset($data[$row['name']])){
                        $data[$row['name']]=$row['default'];
                    }
                }
			}
		}
		return $result;
	}
}
