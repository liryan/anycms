<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Widgets\CategoryWidget;
use App\Models\Category;
use App\Models\DataTable;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{

    public function index(Request $req)
    {
        $id = $req->get("id", 0);
        $cate = new Category();
        if ($req->ajax()) {
            $start = $req->get('start');
            $length = $req->get('length');
            $draw = intval($req->get('draw'));
            $result = $cate->categories($start, $length, $id);
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
            $this->breadcrumb = $cate->getPath($id ? $id : Category::CATEGORY_ID);
            foreach ($this->breadcrumb as &$row) {
                $row['url'] = $this->getUrl() . "?id=" . $row['id'];
            }
            $cat_widget = new CategoryWidget();
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

            $dialog_html = $cat_widget->showEditWidget($urlconfig);
            $list_html = $cat_widget->showListWidget("栏目管理", $urlconfig, $dialog_html);
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
            $cate = new Category();
            $re = $cate->getDataById($id);
            $widgets = new CategoryWidget();
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
                $widget = new CategoryWidget();
                $data = $req->all();
                $widget->tranformSetting($data, true);
                $cate = new Category();
                $result = $cate->addCategory($id, $data);
                return json_encode($result);
                break;

            case "edit":
                $widget = new CategoryWidget();
                $data = $req->all();
                $widget->tranformSetting($data, true);
                $cate = new Category();
                $result = $cate->editCategory($id, $data);
                return json_encode($result);
                break;
        }
    }
    public function getDelete(Request $req)
    {
        $id = $req->get('id');
        $cat = new Category();
        $result = $cat->deleteCat($id);
        if ($result) {
            return json_encode(array('code' => 1));
        }
        return json_encode(array('code' => 0, 'msg' => '删除失败'));
    }

    public function anyModifyorder(Request $req)
    {
      $post = $req->all();
      $data = [];
      foreach($post as $k=>$v){
        if(strpos($k,'order_')!==false){
          $data[str_replace("order_","",$k)]=$v;
        }
      }
      $cat = new Category();
      $num = $cat->modifyOrder($data);
      return json_encode(['code'=>200,'num'=>$num]);
    }
}
