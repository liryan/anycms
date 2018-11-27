<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends WebController
{
    public function index(Request $req)
    {
        $data=$this->getNav();
        $view=$this->View('index');
        $view->with('nav',$data);
        return $view;
    }

    public function category(Request $req,$catid)
    {
        $data=$this->getNav();
        $view=$this->View('list');
        return $view->with('nav',$data);
    }
}
