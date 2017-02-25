<?php
namespace App\Http\Controllers;
use DB;
class IndexController extends Controller
{
    public function anyIndex()
    {

        DB::table("member")->insert(array('email' => 'john@example.com', 'votes' => 0))
    }
}
