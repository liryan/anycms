<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
class UserController extends Controller
{
	public function getLogin()
	{
		return $this->View("login");
	}

	public function postDoLogin(Request $req)
	{
		Auth::attemp($req->get('email'),$req->get('password'));
	}
}
