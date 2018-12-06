<?php

namespace App\Models;

class Member extends BaseSetting
{
    /**
     *
     */
    const TREE_ID = 5;
    public function getRoles()
    {
        return $this->getDataByParentId(self::TREE_ID, true);
    }
}
