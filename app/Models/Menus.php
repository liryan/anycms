<?php

namespace App\Models;

class Menus extends BaseSetting
{
	/**
	*
	*/
	public const TREE_ID=1;
	public function getMenus(){
		return $this->getMenuByParentId(self::TREE_ID,true);
	}
}
