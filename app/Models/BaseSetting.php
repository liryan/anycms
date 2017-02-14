<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseSetting extends Model
{
    protected $table = 't_setting';
	/**
	*
	*/
	protected function getDataPageByParentId($id,$start=0,$length=0)
	{
		if($start==0 && $length==0){
            $data=$this->where('parentid',$id)->orderBy('order','desc')->get();
            if($data){
                return $data->toArray();
            }
            return Array();
        }
		else{
            $data=$this->where('parentid',$id)->orderBy('order','desc')->skip($start)->take($length)->get();
            if($data){
                return $data->toArray();
            }
            return Array();
		}
	}

    protected function getCount($id)
    {
        return $this->where('parentid',$id)->count();
    }

	protected function getDataByParentId($id,$inclsub=false)
	{
		$allrow=[];
		$parentids=[];
		$data=$this->getDataPageByParentId($id,0,0);
		foreach($data as &$row){
			$parentids[]=$row['id'];
			$allrow[$row['id']]=&$row;
		}
		$all_node=$data;
		if($inclsub){
			$deep=0;
			do{
                $subdata=$this->whereIn("parentid",$parentids)->orderBy('order','desc')->get();
                if($subdata){
                    $children=$subdata->toArray();
                    $parentids=[];
    				foreach($children as $cld){
    					$parentids[]=$cld['id'];
    					if(!isset($allrow[$cld['parentid']]['subdata'])){
    						$allrow[$cld['parentid']]['subdata']=[];
    					}
    					$allrow[$cld['parentid']]['subdata'][]=$cld;
    				}
                }
                else{
                    break;
                }
			}while($deep++<100);
		}
		return $data;
	}

    public function getDataById($id)
    {
        $data=$this->where("id",$id)->first();
        if($data){
            return $data->toArray();
        }
        return false;
    }

	protected function newData($parentid,$data)
	{
        $newobj=new BaseSetting();
		$newobj->parentid=$parentid;
		$newobj->setting=$data['setting'];
		$newobj->name=$data['name'];
		$newobj->order=intval($data['order']);
        $newobj->type=intval($data['type']);
        $newobj->note=$data['note'];
		$newobj->save();
	}

	protected function editData($id,$data)
	{
        return $this->where("id",$id)->update($data);
	}

	protected function deleteData($id)
	{
        return $this->where("id",$id)->delete();
	}
}
