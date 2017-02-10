<?php

namespace App\Models;

class ConstDefine extends BaseSetting
{
	public const TREE_CONST_ID=3;
	public function consts($id,$start,$length)
	{
		if($id==0)
			$id=self::TREE_CONST_ID;
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
