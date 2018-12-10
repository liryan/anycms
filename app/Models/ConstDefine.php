<?php

namespace App\Models;

class ConstDefine extends BaseSetting
{
    const TREE_CONST_ID = 3;
    //定义栏目列表字段以及新增栏目的字段
    public static $const_fields = [
        ['name' => 'id', 'note' => '编号', 'comment' => '', 'default' => '', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_INTEGER],
        ['name' => 'name', 'note' => '常量名字', 'comment' => '命名 ', 'default' => '', 'editable' => true, 'listable' => true, 'type' => DataTable::DEF_CHAR],
        ['name' => 'note', 'note' => '备注', 'comment' => '请输入', 'default' => '', 'editable' => true, 'listable' => true, 'type' => DataTable::DEF_CHAR],
        ['name' => 'value', 'note' => '自定义值', 'comment' => '请输入字母', 'default' => '', 'editable' => true, 'listable' => true, 'type' => DataTable::DEF_INTEGER],
        ['name' => 'created_at', 'note' => '创建日期', 'comment' => '', 'default' => '', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_DATE],
        ['name' => '_internal_field', 'note' => '操作', 'comment' => '', 'default' => '11110', 'editable' => false, 'listable' => true, 'type' => DataTable::DEF_INTEGER],
    ];

    public static $setting_field = [
    ];

    public function getConstArray($id)
    {
        $data = $this->getDataPageByParentId($id);
        $re = [];
        foreach ($data as $row) {
            $this->transValue($row);
            $re[$row['value']] = $row['name'];
        }
        return $re;
    }

    public function consts($start, $length, $id)
    {
        if ($id == 0) {
            $id = self::TREE_CONST_ID;
        }

        if ($start < 0) {
            $start = 0;
        }

        if ($length < 1) {
            $length = 20;
        }
        $total = $this->getCount($id);
        $data = $this->getDataPageByParentId($id, $start, $length);
        foreach ($data as &$row) {
            $this->transValue($row);
        }
        return array('total' => $total, 'data' => $data);
    }

    public function addConst($id, $data)
    {
        if ($id <= 0) {
            $id = self::TREE_CONST_ID;
        }

        $newdata = [
            'parentid' => $id,
            'note' => $data['note'],
            'name' => $data['name'],
            'setting' => $data['setting'],
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'order' => 0,
            'type' => 0,
        ];

        $count = $this->where('parentid', $id)->where("name", $data['name'])->count();
        if ($count > 0) {
            return array('code' => 0, 'msg' => '常量已经存在了');
        }

        $this->newData($id, $newdata);
        return array('code' => 1, 'msg' => '成功添加了' . $data['name']);
    }

    public function editConst($id, $data)
    {
        if ($id <= 0) {
            return array('code' => 0, 'msg' => '非法修改');
        }

        $newdata = [
            'name' => $data['name'],
            'note' => $data['note'],
            'setting' => $data['setting'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $olddata = $this->where("id", $id)->first();

        $count = $this->where('parentid', $olddata->parentid)->where("name", $olddata['name'])->count();
        if ($count == 0) {
            return array('code' => 0, 'msg' => '常量不存在');
        }

        $this->editData($id, $newdata);
        return array('code' => 1, 'msg' => '成功修改' . $data['name']);
    }

    public function deleteConst($id)
    {
        $count = $this->where("parentid", $id)->count();
        if ($count > 0) {
            return false;
        }
        $result = $this->deleteData($id);
        return $result > 0;
    }

    public function transValue(&$row)
    {
        if ($row && is_array($row)) {
            $row['value'] = $row['id'];
            $setting = json_decode($row['setting'], true);
            if (isset($setting['value'])) {
                $row['value'] = $setting['value'];
            }
        }
    }
}
