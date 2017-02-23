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

	public function generateSearchCondition($categoryid,$input)
	{
		$this->registerConditionClouser($input);
		if( isset($this->search_widget[$categoryid]) ){
			return ['condition'=>$this->search_widget[$categoryid]['condition'],'order'=>$this->search_widget[$categoryid]['order']($input);
		}
		return Array();
	}
	/**
	 * [registerConditionClouser 注册内容列表的条件搜索闭包]
	 * @method registerConditionClouser
	 * @param  [type]                   $data  [description]
	 * @param  [type]                   $input [description]
	 * @return [type]                          [description]
	 */
	public function registerConditionClouser(&$data,$input)
	{
		$class = new ReflectionClass(__CLASS__);
		$methods = $class->getMethods(ReflectionMethod::IS_PRIVATE);
		foreach($methods as $method){
			if(preg_match("/^search_([0-9])+$/i",$method->name,$match)){
				$this->search_widget[$match[1]]=call_user_method($method->name,$this,[$input]);
			}
		}
	}
	/************************************************
	* 注册搜索框，prototype:  search_categoryid
	* **********************************************/
	provite function search_12($input)
	{
		return [
			'condition'=>function($query) use($input){
			},
			'order'=>function() use($input){
				return "";
			}
		]；
	}
}
