<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Http\Request;

class IndexController extends Controller
{
	public function isStr($str)
	{
		if(preg_match('/a-z0-9\'"\[\]\&/i',$str)){
			return false;
		}
		return true;
	}
    public function anyIndex(Request $req)
    {
        /*
		$tag=$req->get('tag');
		$type=$req->get('type');
		$age=$req->get('age');
		$query=DB::table('t_content')->whereRaw('id>0');
		if($tag){
			if(!$this->isStr($tag)){
				$query=$query->where('tag',$tag);
			}
		}
		if($type){
			if(!$this->isStr($type)){
				$query=$query->where('tag',$type);
			}
		}
		if($age){
			if(!$this->isStr($age)){
				$query=$query->where('tag',$age);
			}
		}
		$data=$query->take(10)->skip(0)->get();
        return $this->View('index')->with('data',$data);
        */
    }
}
