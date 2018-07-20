<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Models\DataTable;
use App\Models\Category;
use App\Models\StatDefine;
use Illuminate\Support\Facades\View;

class StatWidget extends Widget
{
	private static $DIALOG_ID=1;
	public function showListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=$this->getDataTableView();
		$fields=[];
		foreach(StatDefine::$fields as $row){
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
		$view=$this->getView("StatEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach(StatDefine::$fields as $row){
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
			$setting=['tablename'=>$data['tablename'],'condition'=>$data['condition'],'item'=>$data['item']];
			$result['setting']=json_encode($setting);
			$data['setting']=$result['setting'];
            unset($data['tablename']);
            unset($data['condition']);
            unset($data['item']);
		}
		else{
			$obj=json_decode($data['setting'],true);
			if(is_array($obj)){
				foreach($obj as $k=>$v){
					$data[$k]=$v;
				}
			}
		}
		return $result;
	}
}
