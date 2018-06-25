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

    public function getWelcome(Request $req)
    {
        return $this->View("index");
    }
}
