<?php

namespace App\Http\Controllers\Widgets;

use App\Models\DataTable;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class ContentWidget extends Controller
{
	private static $DIALOG_ID=1;
	private $define;
	public function __construct($tableDefine)
	{
		$this->define=$tableDefine;
	}
	public function showListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=View::make("widgets.DataTable");
		$fields=[];
		foreach($this->define['columns'] as $row){
			if($row['listable']){
				$fields[]=$row;
			}
		}
		$view->with("fields",$fields);
		$view->with("name",$name);
		$view->with($urlconfig);
		return $view->with('new_dialog',$new_dialog_html)->render();
	}

	public function showEditWidget($urlconfig)
	{
		$view=View::make("widgets.DataEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach($this->define['columns'] as $row){
			if($row['editable']){
				$fields[]=$row;
			}
		}
		return $view->with(['inputs'=>$fields,'dialog_id'=>self::$DIALOG_ID++])->render();
	}

	public function tranformSetting(&$data,$in)
	{

	}
}
