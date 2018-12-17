<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContentTable;
use App\Models\DataTable;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends WebController
{
    public function index(Request $req)
    {
        $data = $this->getNav();
        $view = $this->View('index');
        $view->with('nav', $data);
        return $view;
    }
    /**
     * process the category of contents
     *
     * @param \Illuminate\Http\Request $req
     * @param int $catid
     * @param int $page
     * @return void
     */
    public function category(Request $req, $catid, $page = 1)
    {
        $pagesize = Config::get("PAGE_SIZE", 20);
        if ($page < 1) {
            $page = 1;
        }
        $start = $pagesize * ($page - 1);
        $cat = new Category();
        $dt = new DataTable();
        $info = $cat->getCategoryInfo($catid);
        $modelid = $info['modelid'];
        $table_define = $dt->tableColumns($modelid);
        if ($info['setting']['nav'] != 1) {
            return redirect("/");
        }
        $view = $this->View($info['setting']['tpl']);
        $content = new ContentTable();
        $list = $content->getList($start, $pagesize, $table_define, $catid);
        $nav = $this->getNav();
        return $view->with('nav', $nav)
            ->with("catid", $catid)
            ->with("description", $info['setting']['description'])
            ->with('page_title', $info['name'])
            ->with('list', $list['data']);
    }

    public function page(Request $req, $catid, $id)
    {
        $cat = new Category();
        $dt = new DataTable();
        $info = $cat->getCategoryInfo($catid);
        $modelid = $info['modelid'];
        $table_define = $dt->tableColumns($modelid);
        $content = new ContentTable();
        $content = $content->getContent($table_define, $id);
        $nav = $this->getNav();

        return $this->View("page")
            ->with('nav', $nav)
            ->with('content', $content);
    }
}
