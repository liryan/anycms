<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * BaseSetting
 *
 * @uses BaseModel
 * @package admlite
 * @version beta-0.0.1
 * @copyright free copyright
 * @author RYan <canbetter@outlook.com>
 * @license MIT
 */
class BaseSetting extends BaseModel
{
    const SYS_ID_LIMIT = 100;
    protected $table = 't_setting';
    /**
     * [getDataPageByParentId 获取某个ID下面的所有的子节点]
     * @method getDataPageByParentId
     * @param  [type]                $id     [根节点ID]
     * @param  integer               $start  [开始]
     * @param  integer               $length [长度]
     * @return [type]                        [数据]
     */
    protected function getDataPageByParentId($id, $start = 0, $length = 0)
    {
        if ($start == 0 && $length == 0) {
            $data = $this->where('parentid', $id)->orderBy('order', 'desc')->get();
            if ($data) {
                return $data->toArray();
            }
            return array();
        } else {
            $data = $this->where('parentid', $id)->orderBy('order', 'desc')->skip($start)->take($length)->get();
            if ($data) {
                return $data->toArray();
            }
            return array();
        }
    }
    /**
     * [getCount 获得子节点个数]
     * @method getCount
     * @param  [type]   $id [description]
     * @return [type]       [description]
     */
    protected function getCount($id)
    {
        return $this->where('parentid', $id)->count();
    }
    /**
     * [getDataByParentId 获取某个ID下面的所有的子节点]
     * @method getDataByParentId
     * @param  [type]            $id      [父节点]
     * @param  boolean           $inclsub [是否包含子节点]
     * @param  clourse           $handleClourse [节点处理闭包，该闭包返回为true的时候，会导致此函数返回对应数据]
     * @return [type]                     [description]
     */
    protected function getDataByParentId($id, $inclsub = false, $dataClourse = null)
    {
        $allrow = [];
        $parentids = [];
        $data = $this->getDataPageByParentId($id, 0, 0);
        foreach ($data as &$row) {
            $parentids[] = $row['id'];
            $allrow[$row['id']] = &$row;
        }
        $all_node = $data;
        if ($inclsub) {
            $deep = 0;
            do {
                $subdata = $this->whereIn("parentid", $parentids)->orderBy('order', 'desc')->get();
                if ($subdata && $subdata->toArray()) {
                    $children = $subdata->toArray();
                    $parentids = [];
                    foreach ($children as $cld) {
                        if ($dataClourse != null) {
                            if ($dataClourse($cld) == true) {
                                return $cld;
                            }
                        }
                        $parentids[] = $cld['id'];
                        if (!isset($allrow[$cld['parentid']]['subdata'])) {
                            $allrow[$cld['parentid']]['subdata'] = [];
                        }
                        $allrow[$cld['parentid']]['subdata'][] = $cld;
                    }
                } else {
                    break;
                }
            } while ($deep++ < 100);
        }
        if ($dataClourse != null) {
            return false;
        }
        return $allrow;
    }

    public function getDataById($id)
    {
        $data = $this->where("id", $id)->first();
        if ($data) {
            return $data->toArray();
        }
        return false;
    }

    protected function newData($parentid, $data)
    {
        $newobj = new BaseSetting();
        $newobj->parentid = $parentid;
        $newobj->setting = $data['setting'];
        $newobj->name = $data['name'];
        $newobj->order = intval($data['order']);
        $newobj->type = intval($data['type']);
        $newobj->note = $data['note'];
        $newobj->save();
    }

    protected function editData($id, $data)
    {
        return $this->where("id", $id)->update($data);
    }

    /**
     * deleteData forbid delete system var
     *
     * @param mixed $id
     * @param mixed $includeChild
     * @access protected
     * @return void
     */
    protected function deleteData($id, $includeChild = false)
    {
        if ($id < self::SYS_ID_LIMIT) {
            return false;
        }
        if ($includeChild) {
            $this->where("parentid", $id)->delete();
        }
        return $this->where("id", $id)->delete();
    }
    /**
     * [getPath 获得到此节点的路径]
     * @method getPath
     * @param  [type]  $id [description]
     * @return [type]      [description]
     */
    public function getPath($id)
    {
        $re = [];
        do {
            $data = $this->where("id", $id)->first();
            if ($data) {
                array_unshift($re, ['note' => $data->note, 'name' => $data->name, 'id' => $data->id]);
                $id = $data->parentid;
            } else {
                break;
            }
            if (sizeof($re) > 10) { //不要超过10级
                break;
            }
        } while (true);
        return $re;
    }

    public function getDataByIds($ids)
    {
        if (!$ids) {
            return [];
        }
        $data = $this->whereIn("id", $ids)->get();
        if ($data) {
            return $data;
        }
        return false;
    }
}
