<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Widgets\MenuWidget;
use App\Models\DataTable;
use App\Models\Privileges;
use Illuminate\Http\Request;

class MenuController extends AdminController
{
    public function index(Request $req)
    {
        $id = $req->get("id", 0);
        $menu = new Privileges();
        if ($req->ajax()) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $result = $menu->getMenus($id, $start, $length);
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
            $this->breadcrumb = $menu->getPath($id ? $id : Privileges::MENU_USR_ID);
            foreach ($this->breadcrumb as &$row) {
                $row['url'] = $this->getUrl() . "?id=" . $row['id'];
            }
            $menu_widget = new MenuWidget();
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

            $dialog_html = $menu_widget->showEditWidget($urlconfig);
            $list_html = $menu_widget->showListWidget("菜单管理", $urlconfig, $dialog_html);
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
            $menu = new Privileges();
            $re = $menu->getDataById($id);
            $widgets = new MenuWidget();
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
        $menu = new Privileges();
        switch ($action) {
            case "add":
                $widget = new MenuWidget();
                $data = array('name' => $req->get('name'), 'note' => $req->get('note'), 'setting' => $req->get('setting'));
                $widget->tranformSetting($data, true);
                $result = $menu->addMenu($id, $data);
                return json_encode($result);
                break;

            case "edit":
                $widget = new MenuWidget();
                $data = array('name' => $req->get('name'), 'note' => $req->get('note'), 'setting' => $req->get('setting'));
                $widget->tranformSetting($data, true);
                $result = $menu->editMenu($id, $data);
                return json_encode($result);
                break;
        }
    }
    public function getDelete(Request $req)
    {
        $id = $req->get('id');
        $cat = new Privileges();
        $result = $cat->deleteMenu($id);
        if ($result) {
            return json_encode(array('code' => 1));
        }
        return json_encode(array('code' => 0, 'msg' => '删除失败'));
    }
}
