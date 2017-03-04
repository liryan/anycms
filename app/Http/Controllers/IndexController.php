<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function anyIndex(Request $req)
    {
        return $this->View('index');
    }
}
