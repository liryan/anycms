<?php
namespace App\Http\Controllers\Admin;
/**
 * ExtController ,扩展菜单的处理入口
 * 这儿修改了访问URL，把/admin/ext/xxx/xxx 修改为/admin/ext/xxx-xxx
 * get /admin/ext/xxx/ccc 对应的处理方法为 ExtController@getXxxCcc
 * 如果获取当前浏览器的url(未修改之前)，使用$_SERVER[REQUEST_URI_ORIGIN]全局变量
 * @uses AdminController
 * @package admlite
 * @version beta-0.0.1
 * @copyright free copyright
 * @author RYan <canbetter@outlook.com> 
 * @license MIT
 */
use Config;
use DB;
use Illuminate\Http\Request;
use App\Models\DataTable;
use App\Models\Privileges;
use App\Http\Requests\MenuModify;
use App\Http\Controllers\Admin\Widgets\MenuWidget;

class ExtController extends AdminController
{
    private $pridb;
    public function __construct()
    {
        parent::__construct();
    }
    public function getTest(Request $req)
    {
        echo "ok";
    }
    public function getStatOrder(Request $req)
    {
        return "OK";
    }
}
