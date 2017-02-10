<?php
namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function anyIndex()
    {
        return $this->View('index');
    }
}
