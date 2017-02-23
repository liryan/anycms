<?php

namespace App\Models;
use DB;
use Config;

class ContentTable extends BaseSetting
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
		if($search){
			if(!isset($search['condition'])){
				$search['condition']=['id','>',0];
			}
			if(!isset($search['order']) ){
				$search['order']='id desc';
			}
		}
		else{
			$search=['condition'=>['id','>',0],'order'=>'id desc'];
		}

		$field=[];
		$query=null;
		foreach($table_define['columns'] as $row){
			if($row['listable']){
				if(!$query){
					$query=DB::table($table_define['name'])->select($row['name']);
				}
				else {
					$query->addSelect($row['name']);
				}
			}
		}
		$count=DB::table($table_define['name'])->where($search['condition'])->count();
		$data=$query->where($search['condition'])->orderByRaw($search['order'])->skip($start)->take($length)->get();
		if($data){
			$data=$data->toArray();
		}
		return Array('total'=>$count,'data'=>$data);
	}
}
