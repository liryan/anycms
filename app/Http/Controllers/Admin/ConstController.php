<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Widgets\ConstWidget;
use App\Models\ConstDefine;
use App\Models\DataTable;
use Illuminate\Http\Request;

class ConstController extends AdminController
{
    public function index(Request $req)
    {
        $id = $req->get("id", 0);
        $cate = new ConstDefine();
        if ($req->ajax()) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $result = $cate->consts($start, $length, $id);
            foreach ($result['data'] as &$row) {
                $row['_internal_field'] = '';
            }
            $data = array(
                "draw" => $draw,
                "recordsTotal" => $result['total'],
                "recordsFiltered" => $result['total'],
                "data" => $result['data'],
            );
            return json_encode($data);
        } else {
            $models = new DataTable();
            $this->breadcrumb = $cate->getPath($id ? $id : ConstDefine::TREE_CONST_ID);
            foreach ($this->breadcrumb as &$row) {
                $row['url'] = $this->getUrl() . "?id=" . $row['id'];
            }
            $const_widget = new ConstWidget();
            $urlconfig = [
                "url" => $this->getUrl() . "?id=$id",
                "edit_url" => $this->getUrl("modify"),
                "view_url" => $this->getUrl("view"),
                "open_url" => $this->geturl(""),
                "delete_url" => $this->getUrl("delete"),
                "field_url" => $this->getUrl("field"),
                "pri" => '11110',
                "id" => $id,
                "models" => $models->tables(0, 200)['data'],
            ];

            $dialog_html = $const_widget->showEditWidget($urlconfig);
            $list_html = $const_widget->showListWidget("常量管理", $urlconfig, $dialog_html);
            return $this->View("index")->with(
                [
                    "table_widget" => $list_html,
                ]
            );
        }
    }

    public function getView(Request $req)
    {
        $id = $req->get('id');
        if ($id == 0) {
            $re['action'] = 'add';
            $re['_token'] = csrf_token();
            return json_encode($re);
        } else {
            $cate = new ConstDefine();
            $re = $cate->getDataById($id);
            $widgets = new ConstWidget();
            $widgets->tranformSetting($re, false);
            $re['action'] = 'edit';
            $re['_token'] = csrf_token();
            return json_encode($re);
        }
    }

    public function postModify(Request $req)
    {
        $id = $req->get("id", 0);
        $action = $req->get('action');
        switch ($action) {
            case "add":
                $widget = new ConstWidget();
                $data = array('value' => $req->get('value'), 'name' => $req->get('name'), 'note' => $req->get('note'));
                $widget->tranformSetting($data, true);
                $const = new ConstDefine();
                $result = $const->addConst($id, $data);
                return json_encode($result);
                break;

            case "edit":
                $widget = new ConstWidget();
                $data = array('value' => $req->get('value'), 'name' => $req->get('name'), 'note' => $req->get('note'));
                $widget->tranformSetting($data, true);
                $const = new ConstDefine();
                $result = $const->editConst($id, $data);
                return json_encode($result);
                break;
        }
    }
    public function getDelete(Request $req)
    {
        $id = $req->get('id');
        $cat = new ConstDefine();
        $result = $cat->deleteConst($id);
        if ($result) {
            return json_encode(array('code' => 1));
        }
        return json_encode(array('code' => 0, 'msg' => '删除失败'));
    }
}
