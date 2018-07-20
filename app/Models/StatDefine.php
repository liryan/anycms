<?php

namespace App\Models;
use DB;
use Config;

class StatDefine extends BaseSetting
{
	//定义栏目列表字段以及新增栏目的字段
	public static $fields=[
		['name'=>'id','note'=>'编号','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
		['name'=>'name','note'=>'统计项目','comment'=>'请输入(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'note','note'=>'备注','comment'=>'请输入(字母)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'setting','note'=>'配置','comment'=>'请输入备注','default'=>'','editable'=>true,'listable'=>false,'type'=>DataTable::DEF_CHAR],
		['name'=>'created_at','note'=>'创建日期','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
		['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER]
	];

	public static $field_setting=[
        ["name"=>"modelid",'note'=>'关联模型','default'=>''],
	];

	const STAT_ID=8;

	public function stats($start,$length,$id=0)
	{
		if($id==0){
			$id=self::STAT_ID;
		}
		if($start<0)
			$start=0;
		if($length<1){
			$length=20;
		}
		$total=$this->getCount($id);
		$data=$this->getDataPageByParentId($id,$start,$length);
		return Array('total'=>$total,'data'=>$data);
	}

    public function getAllStatMenu()
    {
		return $this->getDataByParentId(self::STAT_ID,true);
    }

	public function addStat($id,$data)
	{
		if($id<=0){
			$id=self::STAT_ID;
		}

		$newdata=[
			'parentid'=>$id,
			'note'=>$data['note'],
			'name'=>$data['name'],
			'setting'=>$data['setting'],
			'order'=>0,
			'type'=>0,
		];

		$count=$this->where('parentid',$id)->where("name",$data['name'])->count();
		if($count>0){
			return Array('code'=>0,'msg'=>'统计已经存在了');
		}

		$this->newData($id,$newdata);
		return Array('code'=>1,'msg'=>'成功修改'.$data['note']);
	}

	public function editStat($id,$data)
	{
		if($id<=0){
			return Array('code'=>0,'msg'=>'非法修改');
		}

		$newdata=[
			'note'=>$data['note'],
			'name'=>$data['name'],
			'setting'=>$data['setting'],
		];
		$olddata=$this->where("id",$id)->first();

		$count=$this->where('parentid',$olddata->parentid)->where("name",$data['name'])->count();
		if($count==0){
			return Array('code'=>0,'msg'=>'统计不存在');
		}

		$this->editData($id,$newdata);
		return Array('code'=>1,'msg'=>'成功修改'.$data['note']);
	}

	public function deleteStat($id)
	{
		$count=$this->where("parentid",$id)->count();
		if($count>0){
			return false;
		}
		$result=$this->deleteData($id);
		return $result>0;
	}
}
