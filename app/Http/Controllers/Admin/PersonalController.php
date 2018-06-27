<?php
namespace App\Http\Controllers\Admin;
use Log;
use App;
use Redirect;
use App\Models\User;
use App\Lib\CurlInterface;
use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
class PersonalController extends AdminController
{
    public function index(Request $req)
    {
        return $this->View("index");
    }

    public function getProfile(Request $req)
    {
        return $this->error($req,'错误了');
        //return $this->View("profile");
    }

    public function getSetting(Request $req)
    {
        return $this->View("setting");
    }
}
