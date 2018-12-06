<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $breadcrumb; //面包屑导航数组

    protected function View($name)
    {
        $view = '';
        if ($name[0] == '/') {
            $name = trim($name, "/");
            $view = View::make("templates.admin." . $name);
        } else {
            $view = View::make("templates.admin." . $this->getClassName() . "." . $name);
        }
        return $view;
    }

    protected function getClassName()
    {
        $class_name = get_class($this);
        $path = explode("\\", $class_name);
        if ($path) {
            $class_name = array_pop($path);
        }
        return str_replace("controller", "", strtolower($class_name));
    }

    protected function getUrl($method = '')
    {
        return '/' . $this->getClassName() . '/' . strtolower($method);
    }

    protected function widget($name)
    {
        return View::make("widgets." . $name);
    }

    protected function ajax($code, $msg, $data = [])
    {
        return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    /**
     * getValuesByPrefix
     * 通过前缀获取值 [pre_id => value] => 获取所有的id
     * @param mixed $prefix
     * @param mixed $onlyValues true 只返回所有前缀的id数字，false 返回 id=>value
     * @access protected
     * @return void
     */
    protected function getValuesByPrefix($prefix, $onlyValues = true)
    {
        $data = [];
        foreach ($_POST as $k => $v) {
            if (strpos($k, $prefix) !== false) {
                $key = str_replace($prefix, "", $k);
                if ($onlyValues) {
                    if (strlen($v) > 0 && strlen($key) > 0) {
                        $data[] = $key;
                    }
                } else {
                    $data[$key] = $v;
                }
            }
        }
        return $data;
    }

    protected function removeValueByPrefix(&$data, $prefix)
    {
        foreach ($data as $k => $v) {
            if (strpos($k, $prefix) !== false) {
                unset($data[$k]);
            }
        }
    }
}
