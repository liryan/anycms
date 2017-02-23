<?php

namespace App\Http\Controllers\Widgets;

use App\Models\DataTable;
use App\Models\Category;
use Illuminate\Support\Facades\View;

class ContentWidget extends Widget
{
	private static $DIALOG_ID=1;
	private $define;
	private $search_categoryid;
	private $search_clouser;
	public function __construct($tableDefine)
	{
		$this->define=$tableDefine;
	}

	public function showListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=$this->getDataTableView();
		$fields=[];
		foreach($this->define['columns'] as $row){
			if($row['listable']){
				$fields[]=$row;
			}
		}
		$view->with("fields",$fields);
		$view->with("name",$name);
		$view->with($urlconfig);
		$view->with('new_dialog',$new_dialog_html);
		return $view->render();
	}

	public function showEditWidget($urlconfig)
	{
		$view=$this->getView("DataEdit");
		$view->with($urlconfig);
		$fields=[];
		foreach($this->define['columns'] as $row){
			if($row['editable']){
				$fields[]=$row;
			}
		}
		return $view->with(['inputs'=>$fields,'dialog_id'=>self::$DIALOG_ID++])->render();
	}

	protected function getSearchWidget()
	{
		if($this->search_categoryid==0){
			$view=$this->getView("search.search");
			$fields=[];
			foreach($this->define['columns'] as $row){
				if($row['searchable']){
					$fields[]=['name'=>$row['name'],'note'=>$row['label']];
				}
			}
			$view->with('fields',$fields);
		}
		else{
			$view=$this->getView("search.search_".$this->search_categoryid);
		}
		return $view->render();
	}

	public function generateSearchClouser($categoryid,$input)
	{
		$this->registerConditionClouser($input);
		if(isset($this->search_clouser[$categoryid]) ){
			$this->search_categoryid=$categoryid;
			return ['condition'=>$this->search_clouser[$categoryid]['condition'],'order'=>($this->search_clouser[$categoryid]['order'])($input)];
		}
		$this->search_categoryid=0;
		return ['condition'=>$this->search_clouser[0]['condition'],'order'=>$this->search_clouser[0]['order']($input)];
	}
	/**
	 * [registerConditionClouser 注册内容列表的条件搜索闭包]
	 * @method registerConditionClouser
	 * @param  [type]                   $input [description]
	 * @return [type]                          [description]
	 */
	public function registerConditionClouser($input)
	{
		$class = new \ReflectionClass(__CLASS__);
		$methods = $class->getMethods(\ReflectionMethod::IS_PRIVATE);
		foreach($methods as $method){
			if(preg_match("/^search_([0-9]+)$/i",$method->name,$match)){
				$this->search_clouser[$match[1]]=\call_user_func(array($this,$method->name),$input);
			}
		}
	}
	/************************************************
	* 注册搜索框，prototype:  search_categoryid ,0缺省的搜索
	* **********************************************/
	/**
	 * [search_0 搜索处理函数]
	 * @method search_0
	 * @param  [type]   $input [搜索关键词的输入]
	 * @return [type]          [description]
	 */
	private function search_0($input)
	{
		return [
			'condition'=>function($query)use($input){
				if(!trim(@$input['keyword'])){
					$query->where('id','>',0);
				}
				else{
					$query->where($input['field'],$input['keyword']);
				}

			},
			'order'=>function()use($input){
				return "id desc";
			}
		];
	}
}
