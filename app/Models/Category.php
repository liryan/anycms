<?php

namespace App\Models;
use DB;
use Config;

class Category extends BaseSetting
{
	public static $cate_fields=[
		['name'=>'id','label'=>'编号','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'number'],
		['name'=>'note','label'=>'栏目名字','place_holder'=>'请输入(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'name','label'=>'栏目字母','place_holder'=>'请输入(字母)','default'=>'','editable'=>true,'listable'=>true,'type'=>'text'],
		['name'=>'setting','label'=>'备注','place_holder'=>'请输入备注','default'=>'','editable'=>true,'listable'=>false,'type'=>'html'],
		['name'=>'created_at','label'=>'创建日期','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>'datetime'],
		['name'=>'_internal_field','label'=>'操作','place_holder'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>'text']
	];

	private const CATEGORY_ID=1;
	public function categories($start,$length,$id=0)
	{
		if($id==0){
			$id=self::CATEGORY_ID;
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
}
