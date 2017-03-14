<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Auth;
use App;
use Hash;
use Session;
use App\Lib\CurlInterface;
use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
class UserController extends Controller
{
	public function __construct(CurlInterface $api)
	{
	}

	public function getLogin(Request $req)
	{
		$req->session()->put('return_url',$req->get('return_url'));
		return $this->View("login")->with('prefix',$req->is('admin/*')?'admin':'user');
	}

	public function postDoLogin(Request $req)
	{
		$url=$req->session()->get('return_url');;
		$user=Auth::attempt(['email'=>$req->get('email'),'password'=>$req->get('password')]);
		if(!$user){
			//$res=DB::table("t_admin")->insert(['email'=>$req->get('email'),'password'=>$password]);
			//return redirect($url);
			die('Password is Error');
		}
		else{
			return redirect($url);
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
