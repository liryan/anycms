<?php
namespace App\Http\Controllers\Admin\Widgets;

use App\Models\DataTable;
use App\Models\Privileges;
use Illuminate\Support\Facades\View;

class PrivilegeWidget extends Widget
{
	private static $DIALOG_ID=1;
	private $define;
	private $search_categoryid;
	private $search_clouser;
	public function __construct()
	{
	}

	public function showListWidget($name,$urlconfig,$new_dialog_html="")
	{
		$view=$this->getDataTableView('DataTable');
		$view->with("name",$name);
		$view->with($urlconfig);
        $view->with('fields',Privileges::$fields);
		$view->with('new_dialog',$new_dialog_html);
		return $view->render();
	}

	public function showEditWidget($urlconfig)
	{
		$view=$this->getView("RoleEdit");
		$view->with($urlconfig);
		return $view->with(['dialog_id'=>self::$DIALOG_ID++])->render();
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
                    if($row->$rd['name']){
                        $row->$rd['name']=$const_data[$row->$rd['name']];
                    }
                }
                break;
            case DataTable::DEF_MULTI_LIST:
                $const_data=$const->getConstArray($rd['const']);
                foreach($data as &$row){
                    if(!$row->$rd['name']){
                        continue;
                    }
                    $ids=explode(",",$row->$rd['name']);
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

    public function showPriEditWidget($urlconfig)
    {
        return '';
    }

    public function showPriListWidget($name,$urlconfig,$html)
    {
		$view=$this->getDataTableView('TreeTable');
		$view->with("name",$name);
		$view->with("fields",Privileges::$fields);
		$view->with($urlconfig);
		$view->with('new_dialog',$html);
		return $view->render();
    }
}
