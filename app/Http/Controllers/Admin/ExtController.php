<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

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
