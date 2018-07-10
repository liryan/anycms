<?php

namespace App\Models;
use DB;
use Config;

class ContentTable extends BaseModel
{
	/**
	 * [getList 读取数据列表]
	 * @method getList
	 * @param  [type]  $start        [description]
	 * @param  [type]  $length       [description]
	 * @param  [type]  $table_define [description]
	 * @param  [type]  $id           [description]
	 * @param  [type]  $search       [Array('condition'=>Clouser闭包条件,'order'=>string]
	 * @return [type]                [description]
	 */
	public function getList($start,$length,$table_define,$id,$search=[])
	{
		$field=[];
		$query=null;
		foreach($table_define['columns'] as $row){
			if($row['listable']){
				if(!$query){
					$query=DB::table($table_define['info']['name'])->select($row['name']);
				}
				else {
					$query->addSelect($row['name']);
				}
			}
		}
        try{
            $count=DB::table($table_define['info']['name'])->where($search['condition'])->count();
            $data=$query->where($search['condition'])->orderByRaw($search['order'])->skip($start)->take($length)->get();
            if($data){
                $data=$data->toArray();
            }

            return Array('total'=>$count,'data'=>$data);
        }
        catch(\Illuminate\Database\QueryException $e){
            return Array('total'=>0,'data'=>[]);
        }
	}
    public function getContent($define,$id)
    {
        $row=DB::table($define['info']['name'])->where('id',$id)->first();
        if($row){
            return json_decode(json_encode($row),true);
        }
        return $row;  
    }

    public function editContent($define,$id,$data,$catid)
    {
        $data['updated_at']=date('Y-m-d H:i:s');
        $id=DB::table($define['info']['name'])->where('category',$catid)->where('id',$id)->update($data);     
        return $id;
    }

    public function editContentBatch($define,$ids,$data,$catid)
    {
        $data['updated_at']=date('Y-m-d H:i:s');
        $id=DB::table($define['info']['name'])->where('category',$catid)->whereIn('id',$ids)->update($data);     
        return $id;
    }

    public function addContent($define,$data)
    {
        $id=DB::table($define['info']['name'])->insertGetId($data);     
        return $id>0;
    }

    public function deleteContent($define,$id,$catid)
    {
        return DB::table($define['info']['name'])->where('id',$id)->where('category',$catid)->delete();
    }

    public function deleteContentBatch($define,$ids,$catid)
    {
        return DB::table($define['info']['name'])->where('category',$catid)->whereIn('id',$ids)->delete();
    }
}
