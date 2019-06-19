<?php

namespace App\Models;

class Category extends BaseSetting
{
    //定义栏目列表字段以及新增栏目的字段
    public static $cate_fields = [
        ['name' => 'id', 'note' => '编号', 'comment' => '', 'default' => '', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_INTEGER],
        ['name' => 'name', 'note' => '栏目名字', 'comment' => '请输入(中文)', 'default' => '', 'editable' => true, 'listable' => true, 'type' => DataTable::DEF_CHAR],
        ['name' => 'note', 'note' => '栏目字母', 'comment' => '请输入(字母)', 'default' => '', 'editable' => true, 'listable' => true, 'type' => DataTable::DEF_CHAR],
        ['name' => 'setting', 'note' => '备注', 'comment' => '请输入备注', 'default' => '', 'editable' => true, 'listable' => false, 'type' => DataTable::DEF_CHAR],
        ['name' => 'created_at', 'note' => '创建日期', 'comment' => '', 'default' => '', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_DATE],
        ['name' => '_internal_field', 'note' => '操作', 'comment' => '', 'default' => '11111', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_INTEGER],
    ];

    public static $field_setting = [
        ["name" => "modelid", 'note' => '关联模型', 'default' => ''],
        ["name" => "nav", 'note' => '前端导航', 'default' => '0'],
        ["name" => "tpl", 'note' => '模板选定', 'default' => 'list'],
        ['name' => "description", "note" => "描述", 'default' => ''],
    ];

    const CATEGORY_ID = 1;

    public function categories($start, $length, $id = 0)
    {
        if ($id == 0) {
            $id = self::CATEGORY_ID;
        }
        if ($start < 0) {
            $start = 0;
        }

        if ($length < 1) {
            $length = 20;
        }
        $total = $this->getCount($id);
        $data = $this->getDataPageByParentId($id, $start, $length);
        return array('total' => $total, 'data' => $data);
    }
    /**
     * [addCategory 添加新的栏目]
     * @method addCategory
     * @param  [type]      $id   [父栏目ID]
     * @param  [type]      $data [description]
     */
    public function addCategory($id, $data)
    {
        if ($id <= 0) {
            $id = self::CATEGORY_ID;
        }

        $newdata = [
            'parentid' => $id,
            'note' => $data['note'],
            'name' => $data['name'],
            'setting' => $data['setting'],
            'order' => 0,
            'type' => 0,
        ];

        $count = $this->where('parentid', $id)->where("name", $data['name'])->count();
        if ($count > 0) {
            return array('code' => 0, 'msg' => '栏目已经存在了');
        }

        $this->newData($id, $newdata);
        return array('code' => 1, 'msg' => '成功修改' . $data['name']);
    }

    public function editCategory($id, $data)
    {
        if ($id <= 0) {
            return array('code' => 0, 'msg' => '非法修改');
        }

        $newdata = [
            'note' => $data['note'],
            'name' => $data['name'],
            'setting' => $data['setting'],
        ];
        $olddata = $this->where("id", $id)->first();

        $count = $this->where('parentid', $olddata->parentid)->where("name", $data['name'])->count();
        if ($count > 0) {
            return array('code' => 0, 'msg' => '栏目名已存在');
        }

        $this->editData($id, $newdata);
        return array('code' => 1, 'msg' => '成功修改' . $data['name']);
    }

    public function deleteCat($id)
    {
        $count = $this->where("parentid", $id)->count();
        if ($count > 0) {
            return false;
        }
        $result = $this->deleteData($id);
        return $result > 0;
    }

    public function getAllCategory()
    {
        return $this->getDataByParentId(self::CATEGORY_ID, true);
    }

    public function getCategoryInfo($id)
    {
        $data = $this->getDataById($id);
        if ($data) {
            $obj = json_decode($data['setting'], true);
            return ['modelid' => $obj['modelid'], 'name' => $data['name'], 'setting' => $obj];
        }
        return 0;
    }

    public function getModelId($id)
    {
        $data = $this->getDataById($id);
        if ($data) {
            $obj = json_decode($data['setting'], true);
            return $obj['modelid'];
        }
        return 0;
    }

    public function modifyOrder($data)
    {
      $total = 0;
      foreach($data as $k=>$v){
        $total += $this->editData($k, ['order'=>$v,'updated_at'=>date('Y-m-d H:i:s')]);
      }
      return $total;
    }
}
