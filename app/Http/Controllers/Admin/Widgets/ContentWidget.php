<?php
namespace App\Http\Controllers\Admin\Widgets;

use App\Models\Category;
use App\Models\ConstDefine;
use App\Models\DataTable;

class ContentWidget extends Widget
{
    private static $DIALOG_ID = 1;
    private $define;
    private $search_categoryid;
    private $search_clouser;
    public function __construct($tableDefine)
    {
        $this->define = $tableDefine;
    }

    public function showListWidget($name, $urlconfig, $new_dialog_html = "")
    {
        $view = $this->getDataTableView();
        $fields = [];
        foreach ($this->define['columns'] as $row) {
            if ($row['listable']) {
                $fields[] = $row;
            }
        }
        $fields[] = array_pop(DataTable::$fields_it);
        $view->with("fields", $fields);
        $view->with("name", $name);
        $view->with($urlconfig);
        $view->with('new_dialog', $new_dialog_html);
        return $view->render();
    }

    public function showViewWidget($data)
    {
        $view = $this->getView("DataView");
        $fields = $this->translateToView($data);
        return $view->with("fields", $fields);
    }

    public function showEditWidget($urlconfig)
    {
        $view = $this->getView("DataEdit");
        $view->with($urlconfig);
        $fields = [];
        $const = new ConstDefine();
        foreach ($this->define['columns'] as $row) {
            if ($row['type'] == DataTable::DEF_LIST || $row['type'] == DataTable::DEF_MULTI_LIST) {
                $subdata = $const->consts(0, 200, $row['const']);
                if ($subdata['total'] > 0) {
                    $row['const_list'] = $subdata['data'];
                } else {
                    $row['const_list'] = [];
                }
            }
            if ($row['editable']) {
                $fields[] = $row;
            }
        }
        return $view->with(['inputs' => $fields, 'dialog_id' => self::$DIALOG_ID++])->render();
    }

    protected function getSearchWidget()
    {
        if ($this->search_categoryid == 0) {
            $view = $this->getView("search.search");
            $fields = [];
            foreach ($this->define['columns'] as $row) {
                if ($row['searchable']) {
                    $fields[] = ['name' => $row['name'], 'note' => $row['note']];
                }
            }
            $view->with('fields', $fields);
        } else {
            $view = $this->getView("search.search_" . $this->search_categoryid);
        }
        return $view->render();
    }

    /**
     * 见本类的函数search_0的例子
     * [generateSearchClouser 获取对应ID的搜索闭包]
     * @method generateSearchClouser
     * @param  [type]                $categoryid [description]
     * @param  [type]                $input      [description]
     * @return [type]                            [description]
     */
    public function generateSearchClouser($categoryid, $input)
    {
        $this->registerConditionClouser($input);
        if (isset($this->search_clouser[$categoryid])) {
            $this->search_categoryid = $categoryid;
            return ['condition' => $this->search_clouser[$categoryid]['condition'], 'order' => $this->search_clouser[$categoryid]['order']($input)];
        }
        $this->search_categoryid = 0;
        return ['condition' => $this->search_clouser[0]['condition'], 'order' => $this->search_clouser[0]['order']($input)];
    }
    /**
     * [registerConditionClouser 注册内容列表的条件搜索闭包，搜索所有的search_xxx函数]
     * @method registerConditionClouser
     * @param  [type]                   $input [description]
     * @return [type]                          [description]
     */
    public function registerConditionClouser($input)
    {
        $class = new \ReflectionClass(__CLASS__);
        $methods = $class->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if (preg_match("/^search_([0-9]+)$/i", $method->name, $match)) {
                $this->search_clouser[$match[1]] = \call_user_func(array($this, $method->name), $input);
            }
        }
    }

    public function translateToView(&$data)
    {
        $tmp = [json_decode(json_encode($data))];
        $this->translateData($tmp);
        $re = [];
        foreach ($tmp[0] as $k => $v) {
            foreach ($this->define['columns'] as $rd) {
                if ($k != $rd['name']) {
                    continue;
                }
                if ($rd['type'] == DataTable::DEF_IMAGE) {
                    if ($v) {
                        $re[] = ['name' => $k, 'note' => $rd['note'], 'value' => "<img style='max-width:100px' src='" . $v . "'>"];
                    }
                } else if ($rd['type'] == DataTable::DEF_IMAGES) {
                    $value = '';
                    if ($v && is_array($v)) {
                        foreach ($v as $img) {
                            $value .= "<img style='max-width:100px;margin:3px' src='" . $img . "'>";
                        }
                    }
                    $re[] = ['name' => $k, 'note' => $rd['note'], 'value' => $value];
                } else {
                    $re[] = ['name' => $k, 'note' => $rd['note'], 'value' => $v];
                }
            }
        }
        return $re;
    }

    /**
     * translateData
     * 把数据库转换成易读的数据
     * @param mixed $data
     * @param mixed $skipList
     * @access public
     * @return void
     */
    public function translateField(&$row)
    {
        $const = new ConstDefine();
        foreach ($this->define['columns'] as $rd) {
            switch ($rd['type']) {
                case DataTable::DEF_MULTI_LIST:
                    if (!property_exists($row, $rd['name'])) {
                        continue;
                    }
                    if (!strlen($rd['name']) == 0) {
                        continue;
                    }
                    $ids = explode(",", $row->{$rd['name']});
                    foreach ($ids as $id) {
                        $row[$rd['name'] . "_" . $id] = 1;
                    }
                    break;
            }
        }
    }
    /**
     * translateData
     *
     * @param  mixed $data
     * @param  mixed $skipList = false, 多选则会把 "1,2,3,4"=>"name1,name2,name3,name4"
     *
     * @return void
     */
    public function translateData(&$data, $skipList = false)
    {
        $const = new ConstDefine();
        foreach ($this->define['columns'] as $rd) {
            switch ($rd['type']) {
                case DataTable::DEF_LIST:
                    if ($skipList) {
                        break;
                    }
                    $const_data = $const->getConstArray($rd['const']);
                    foreach ($data as &$row) {
                        if (property_exists($row, $rd['name']) > 0) {
                            if (isset($const_data[$row->{$rd['name']}])) {
                                $row->{$rd['name']} = $const_data[$row->{$rd['name']}];
                            } else {
                                $row->{$rd['name']} = '';
                            }

                        } else {
                            $row->{$rd['name']} = '';
                        }
                    }
                    break;
                case DataTable::DEF_MULTI_LIST:
                    if ($skipList) {
                      foreach ($data as &$row) {
                        $ids = explode(",", $row->{$rd['name']});
                        foreach($ids as $id){
                          if($id){
                            $row->{$rd['name']."_".$id}=1;
                          }
                        }
                      }
                    }
                    else{
                      $const_data = $const->getConstArray($rd['const']);
                      foreach ($data as &$row) {
                          if (!property_exists($row, $rd['name'])) {
                              continue;
                          }
                          if (strlen($rd['name']) == 0) {
                              continue;
                          }
                          $ids = explode(",", $row->{$rd['name']});
                          $tmp = [];
                          foreach ($ids as $id) {
                            if($id){
                              $tmp[] = $const_data[$id];
                            }
                          }
                          if($tmp){
                            $row->{$rd['name']} = implode(",", $tmp);
                          }
                          else{
                            $row->{$rd['name']} ='';
                          }
                      }
                    }
                    break;
                case DataTable::DEF_IMAGES:
                    foreach ($data as &$row) {
                        if (property_exists($row, $rd['name']) && $row->{$rd['name']}) {
                            $row->{$rd['name']} = explode(",", $row->{$rd['name']});
                        } else {
                            $row->{$rd['name']} = [];
                        }

                    }
                    break;
            }
        }
    }
    /************************************************
     * 注册搜索框，prototype:  search_categoryid ,0缺省的搜索
     * 1.首先在templates/widgets/search/目录下创建search_xxx.blade.php
     * ,这是模板要包含搜索表单,xxx为频道的id编号
     * 2.在这儿定义一个新的搜索方法 search_xxx
     * 3.xxx为要定制搜索表单的内容频道ID号码，展现这个频道内容列表的时候
     * 就会用对应的搜索框
     * 4.构建condition闭包函数，在里面增加查询条件，input含有搜索表单提交的所有的元素
     * **********************************************/
    /**
     * [search_0 搜索处理函数]
     * @method search_0
     * @param  [type]   $input [搜索关键词的输入]
     * @return [type]          [description]
     */
    private function search_0($input)
    {
        return [
            'condition' => function ($query) use ($input) {
                if (!trim(@$input['keyword'])) {
                    $query->where('id', '>', 0);
                    return;
                }
                $compare = $input['math'];
                if ($compare == 'between') {
                    $fields = explode(",", trim(@$input['keyword']));
                    if (count($fields) < 2) {
                        $query->where($input['field'], "=", $fields[0]);
                    } else {
                        $query->where($input['field'], "<=", max($fields[0], $fields[1]));
                        $query->where($input['field'], ">=", min($fields[0], $fields[1]));
                    }
                } else if ($compare == 'like') {
                    $query->where($input['field'], $compare, "%" . $input['keyword'] . "%");
                } else {
                    $query->where($input['field'], $compare, $input['keyword']);
                }
            },
            'order' => function () use ($input) {
                if (isset($input['order'])) {
                    $col = $input['order'][0]['column'];
                    $dir = $input['order'][0]['dir'];
                    return sprintf("%s %s", $input['columns'][$col]['data'], $dir);
                }
                return "id desc";
            },
        ];
    }
}
