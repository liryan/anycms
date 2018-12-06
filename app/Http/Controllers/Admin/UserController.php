<?php
namespace App\Http\Controllers\Admin;

use App;
use App\Models\Privileges;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Session;

class UserController extends Controller
{
    public function getLogin(Request $req)
    {
        $req->session()->put('return_url', $req->get('return_url'));
        return $this->View("login")->with('prefix', $req->is('admin/*') ? 'admin' : 'user');
    }

    public function checkLogin(Request $req)
    {
        $user = Auth::check();
        if ($user) {
            $url = $req->session()->get('return_url');
            if ($url) {
                return redirect($url);
            } else {
                return redirect("/admin/personal");
            }
        } else {
            return redirect("/admin/login");
        }
    }

    public function postDoLogin(Request $req)
    {
        $url = $req->session()->get('return_url');
        $password = $req->get('password');
        $user = Auth::attempt(['email' => $req->get('email'), 'password' => $password]);
        if (!$user) {
            $obj = $this->ajax(0, '密码或账号错误');
            return $obj;
        } else {
            $user = Auth::user();
            if ($user->status == 0) {
                return $this->ajax(0, '账号已经冻结');
            }
            $role = Auth::user()->role;
            $pridb = new Privileges();
            if ($role == Privileges::ADMIN_ROLE) {
                session()->put('admin', 1);
                session()->put('adminid', Auth::user()->id);
            } else {
                $data = $pridb->loadUserPri($role, null);
                session()->put('pridata', $data);
                session()->put('adminid', Auth::user()->id);
                session()->put('admin', 0);
            }
            if ($url) {
                return $this->ajax(1, '', ['url' => $url]);
            } else {
                return $this->ajax(1, '', ['url' => "/admin/index"]);
            }
        }
    }

    public function getLogout(Request $req)
    {
        Auth::logout();
        if ($req->is("admin/*")) {
            return redirect("admin/login");
        } else {
            return redirect("login");
        }
    }
}
