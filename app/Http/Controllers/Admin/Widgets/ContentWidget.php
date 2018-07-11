<?php
namespace App\Http\Controllers\Admin\Widgets;

use App\Models\DataTable;
use App\Models\Category;
use App\Models\ConstDefine;
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
        $fields[]=array_pop(DataTable::$fields_it);
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
        $const=new ConstDefine();
		foreach($this->define['columns'] as $row){
            if($row['type']==DataTable::DEF_LIST||$row['type']==DataTable::DEF_MULTI_LIST){
                $subdata=$const->consts(0,200,$row['const']);
                if($subdata['total']>0){
                    $row['const_list']=$subdata['data'];
                }
            }
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
					$fields[]=['name'=>$row['name'],'note'=>$row['note']];
				}
			}
			$view->with('fields',$fields);
		}
		else{
			$view=$this->getView("search.search_".$this->search_categoryid);
		}
		return $view->render();
	}

	/**
	 * [generateSearchClouser 获取对应ID的搜索闭包]
	 * @method generateSearchClouser
	 * @param  [type]                $categoryid [description]
	 * @param  [type]                $input      [description]
	 * @return [type]                            [description]
	 */
	public function generateSearchClouser($categoryid,$input)
	{
		$this->registerConditionClouser($input);
		if(isset($this->search_clouser[$categoryid]) ){
			$this->search_categoryid=$categoryid;
			return ['condition'=>$this->search_clouser[$categoryid]['condition'],'order'=>$this->search_clouser[$categoryid]['order']($input)];
		}
		$this->search_categoryid=0;
		return ['condition'=>$this->search_clouser[0]['condition'],'order'=>$this->search_clouser[0]['order']($input)];
	}
	/**
	 * [registerConditionClouser 注册内容列表的条件搜索闭包，搜索所有的search_xxx函数]
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
    public function translateToView(&$data)
    {
        foreach($this->define['columns'] as $rd){
            switch($rd['type']){
            case DataTable::DEF_MULTI_LIST:
                $content=$data[$rd['name']];
                if(trim($content)){
                    if(strpos($content,",")!==false){
                        $data[$rd['name']]=explode(",",$content);
                    }
                    else{
                        $data[$rd['name']]=[$content];
                    }
                }
                else{
                    $data[$rd['name']]=[];
                }
                break;
            }
        }
    }
    public function translateData(&$data)
    {
        $const=new ConstDefine();
        foreach($this->define['columns'] as $rd){
            switch($rd['type']){
            case DataTable::DEF_LIST:
                $const_data=$const->getConstArray($rd['const']);
                foreach($data as &$row){
                    if(property_exists($row,$rd['name']) && $row->{$rd['name']}){
                        if(isset($const_data[$row->{$rd['name']}])){
                            $row->{$rd['name']}=$const_data[$row->{$rd['name']}];
                        }
                        else
                            $row->{$rd['name']}='';
                    }
                    else{
                        $row->{$rd['name']}='';
                    }
                }
                break;
            case DataTable::DEF_MULTI_LIST:
                $const_data=$const->getConstArray($rd['const']);
                foreach($data as &$row){
                    if(!$row->$rd['name']){
                        continue;
                    }
                    $ids=explode(",",$row->{$rd['name']});
                    $tmp=[];
                    foreach($ids as $id){
                        $tmp[]=$const_data[$id];
                    }
                    $row->$rd['name']=implode(",",$tmp);
                }
                break;
            case DataTable::DEF_IMAGES:
                if($row->$rd['name'])
                    $row->$rd['name']=json_decode($row->$rd['name']);
                else
                    $row->$rd['name']=[];
                break;
            }
        }
    }
	/************************************************
	* 注册搜索框，prototype:  search_categoryid ,0缺省的搜索
	* 1.首先在templates/widgets/search/目录下创建search_xxx.blade.php
	* ,这是模板要包含搜索表单
	* 2.在这儿定义一个新的搜索方法 search_xxx
	* 3.xxx为要定制搜索表单的内容频道ID号码，展现这个频道内容列表的时候
	* 就会用对应的搜索框
	* 4.构建condition闭包函数，在里面增加查询条件，input含有搜索表单提交的所有的元素
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
