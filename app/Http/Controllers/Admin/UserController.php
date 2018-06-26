<?php
namespace App\Http\Controllers\Admin;
use DB;
use Log;
use Auth;
use App;
use Hash;
use Session;
use Redirect;
use App\Models\User;
use App\Models\Privileges;
use App\Lib\CurlInterface;
use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
class UserController extends Controller
{
	public function getLogin(Request $req)
	{
		$req->session()->put('return_url',$req->get('return_url'));
		return $this->View("login")->with('prefix',$req->is('admin/*')?'admin':'user');
	}

    public function checkLogin(Request $req)
    {
        $user=Auth::check();
        if($user){
		    $url=$req->session()->get('return_url');;
            if($url){
			    return redirect($url);
            }
            else{
			    return redirect("model");
            }
        }
    }

	public function postDoLogin(Request $req)
	{
		$url=$req->session()->get('return_url');;
        $password=$req->get('password');
		$user=Auth::attempt(['email'=>$req->get('email'),'password'=>$password]);
		if(!$user){
            $obj=$this->ajax(0,'密码或账号错误');
            return $obj;
		}
		else{
            $role=Auth::user()->role;
            $pridb=new Privileges();
            $data=$pridb->loadUserPri($role,null);
            session()->put('pridata',$data);
            if($url){
                return $this->ajax(1,'',['url'=>$url]);
            }
            else{
                return $this->ajax(1,'',['url'=>"/admin/index"]);
            }
		}
	}

	public function getLogout(Request $req)
	{
		Auth::logout();
		if($req->is("admin/*")){
			return redirect("admin/login");
		}
		else{
			return redirect("login");
		}
	}

}
