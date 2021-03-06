<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Widgets\ModelWidget;
use App\Models\ConstDefine;
use App\Models\DataTable;
use Illuminate\Http\Request;

class ModelController extends AdminController
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $dt = new DataTable();
            $result = $dt->tables($start, $length);
            (new ModelWidget())->translateData($result['data']);
            $data = array(
                "draw" => $draw,
                "recordsTotal" => $result['total'],
                "recordsFiltered" => $result['total'],
                "data" => $result['data'],
            );

            return json_encode($data);
        } else {
            $table_widget = new ModelWidget();

            $urlconfig = [
                "url" => $this->getUrl(),
                "edit_url" => $this->getUrl("modify"),
                "view_url" => $this->getUrl("view"),
                "delete_url" => $this->getUrl("delete"),
                "field_url" => $this->getUrl("field"),
                "open_url" => '',
                "pri" => '11111',
            ];
            $dialog_html = $table_widget->showModelEditWidget($urlconfig);
            $list_html = $table_widget->showModelListWidget("模型列表", $urlconfig, $dialog_html);

            return $this->View("models")->with(
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
            $table_widget = new ModelWidget();
            $re = $table_widget->getDefaultModelDefine();
            $re['action'] = 'add';
            $re['_token'] = csrf_token();
            return json_encode($re);
        } else {
            $tb = new DataTable();
            $re = $tb->getDataById($id);
            if ($re['setting']) {
                $setting = json_decode($re['setting'], true);
                $re['url'] = isset($setting['url']) ? $setting['url'] : '';
            } else {
                $re['url'] = '';
            }
            $re['action'] = 'edit';
            $re['_token'] = csrf_token();
            return json_encode($re);
        }
    }

    public function postModify(Request $req)
    {

        $action = $req->get('action');
        switch ($action) {
            case "add":
                $name = $req->get('name');
                $note = $req->get('note');
                $url = $req->get('url');
                $setting = ['url' => $url];
                $dtmodel = new DataTable();
                $result = $dtmodel->addTable($name, array('note' => $note, 'setting' => json_encode($setting)));
                return json_encode($result);
                break;

            case "edit":
                $id = $req->get('id');
                $note = $req->get('note');
                $url = $req->get('url');
                $setting = ['url' => $url];

                $dtmodel = new DataTable();
                $result = $dtmodel->editTable($id, array('note' => $note, 'setting' => json_encode($setting)));
                return json_encode($result);
                break;
        }
    }
    public function getDelete(Request $req)
    {
        $id = $req->get('id');
        $dtmodel = new DataTable();
        $result = $dtmodel->deleteTable($id);
        if ($result) {
            return json_encode(array('code' => 1));
        }
        return json_encode(array('code' => 0, 'msg' => '表中含有数据,不能删除'));
    }

    public function getField(Request $req)
    {
        $id = $req->get('id');
        if ($id < 1) {
            //Redirect::to($this->getUrl());
        }
        if ($req->ajax()) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $all = intval($req->get('all'));
            $dt = new DataTable();
            $result = $dt->fields($id, $start, $length, $all);
            $widget = new ModelWidget();
            $widget->translateData($result['data']);
            $data = array(
                "draw" => $draw,
                "recordsTotal" => $result['total'],
                "recordsFiltered" => $result['total'],
                "data" => $result['data'],
            );

            return json_encode($data);
        } else {
            $table_widget = new ModelWidget();
            $urlconfig = [
                "url" => $this->getUrl('field') . "?id=" . $id,
                "edit_url" => $this->getUrl("modifyfield"),
                "view_url" => $this->getUrl("viewfield"),
                "delete_url" => $this->getUrl("deletefield"),
                "field_url" => '',
                "open_url" => '',
                "pri" => '11110',
            ];
            $dt = new DataTable();
            $tableinfo = $dt->getDataById($id);
            $dialog_html = $table_widget->showFieldEditWidget(array("url" => $this->getUrl('field'), "const_url" => $this->getUrl("consts"), 'modelid' => $id));
            $list_html = $table_widget->showFieldListWidget(sprintf("模型-%s[%s]", $tableinfo['note'], $tableinfo['name']), $urlconfig, $dialog_html);
            return $this->View("models")->with(
                [
                    "table_widget" => $list_html,
                ]
            );
        }
    }
    /**
     * [postModifyField description]
     * @method postModifyField
     * @param  Request         $req [description]
     * @return [type]               [description]
     */
    public function postModifyField(Request $req)
    {
        $modelid = $req->get('modelid');
        $action = $req->get('action');
        if ($modelid < 1) {
            return;
        }
        switch ($action) {
            case "add":
                $fieldwidget = new ModelWidget();
                $allinput = $req->all();
                $setdata = $fieldwidget->tranformFieldSetting($allinput, true);
                $data = array(
                    'note' => $req->get("note"),
                    'name' => $req->get("name"),
                    'type' => $req->get("type"),
                    'setting' => $setdata["setting"],
                    'order' => 0,
                );
                $dtmodel = new DataTable();
                $result = $dtmodel->addField($modelid, $setdata['type'], $data);
                return json_encode($result);
                break;

            case "edit":
                $id = $req->get('id');
                $note = $req->get('note');
                $name = $req->get("name");
                $allinput = $req->all();
                $fieldwidget = new ModelWidget();
                $setdata = $fieldwidget->tranformFieldSetting($allinput, true);
                $dtmodel = new DataTable();
                $result = $dtmodel->editField($id, array('name' => $name, 'note' => $note, 'type' => $setdata['type'], 'setting' => $setdata["setting"]));
                return json_encode($result);
                break;
        }
    }

    public function getViewField(Request $req)
    {
        $id = $req->get('id');
        if ($id == 0) {
            $table_widget = new ModelWidget();
            $re = $table_widget->getDefaultFieldDefine();
            $re['action'] = 'add';
            $re['_token'] = csrf_token();
            return json_encode($re);
        } else {
            $tb = new DataTable();
            $re = $tb->getDataById($id);
            $table_widget = new ModelWidget();
            $table_widget->tranformFieldSetting($re, false);
            if ($table_widget->isConstField($re)) {
                $data = $tb->getDataById($re['const']);
                $re['const_parentid'] = $data['parentid'];
            }

            $re['action'] = 'edit';
            $re['_token'] = csrf_token();
            return json_encode($re);
        }
    }

    public function getDeleteField(Request $req)
    {
        $id = $req->get('id');
        $dtmodel = new DataTable();
        $result = $dtmodel->deleteField($id);
        if ($result) {
            return json_encode(array('code' => 1));
        }
        return json_encode(array('code' => 0, 'msg' => '删除失败'));
    }

    public function getConsts(Request $req)
    {
        $id = $req->get('id', 0);
        if ($id < 1) {
            //Redirect::to($this->getUrl());
            $id = 0;
        }
        if ($req->ajax() || true) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $cd = new ConstDefine();
            $result = $cd->consts($start, $length, $id);

            $data = array(
                "draw" => $draw,
                "recordsTotal" => $result['total'],
                "recordsFiltered" => $result['total'],
                "data" => $result['data'],
            );
            $curdata = $cd->getDataById($id);
            if ($curdata) {
                $data['parentname'] = $curdata["name"];
            }
            return json_encode($data);
        }

    }

}
