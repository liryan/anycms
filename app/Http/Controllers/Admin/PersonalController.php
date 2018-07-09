<?php
namespace App\Http\Controllers\Admin;
use Log;
use App;
use Hash;
use Auth;
use Redirect;
use App\Models\User;
use App\Lib\CurlInterface;
use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
use EasyThumb\EasyThumb;
class PersonalController extends AdminController
{
    public function index(Request $req)
    {
        return $this->View("index");
    }

    public function getProfile(Request $req)
    {
        $config=Array(
            'profile_url'=>$this->getUrl('view'),
            'modifyprofile_url'=>$this->getUrl('modify')
        );
        return $this->View("profile")->with($config);
    }

    public function getView(Request $req)
    {
        $user=new User();
        $data=$user->getUserData($req->session()->get('adminid'));
        $data['_token']=csrf_token();
        unset($data['status']);
        unset($data['updated_at']);
        unset($data['created_at']);
        unset($data['role']);
        unset($data['password']);
        return $this->ajax(1,'',$data);
    }

    public function postModify(Request $req)
    {
        $userid= $req->session()->get('adminid');
        if(!$userid){
            return $this->ajax(0,'请重新登录',[]);
        }
        $old_password=$req->get('old_password');
		$user=Auth::attempt(['id'=>$userid,'password'=>$old_password]);
        if(!$user){
            return $this->ajax(0,'修改失败，原密码不对');
        }
        $user=new User();
        $password=$req->get('password');
        $data=[];
        if(trim($password)){
            $repassword=trim($req->get('repassword'));
            if($password!=$repassword){
                return $this->ajax(0,'两次密码不一致');
            }
            if(mb_strlen($password,"UTF-8")<6){
                return $this->ajax(0,'密码至少６个字符');
            }
            $data['password']=$password;
        }
        $name=trim($req->get('name'));
        if($name && mb_strlen($name,"UTF-8")>10){
            $data['name']=$name;
        }
        $data['updated_at']=date('Y-m-d H:i:s');
        if($req->hasFile('avatar')){
            $root_path=public_path();
            $sub_path="/avatar";
            $path=EasyThumb::upload('avatar')
                ->where($root_path.$sub_path)
                ->autodir(EasyThumb::TIME_DIR)
                ->limit(500*500,EasyThumb::PNG|EasyThumb::JPG)
                ->size(100,100,EasyThumb::SCALE_FREE)
                ->done();
            $data['avatar']=str_replace($root_path,'',$path['origin']);
        }
        $re=$user->modifyUser($userid,$data);
        return json_encode($re);
    }

    public function getSetting(Request $req)
    {
        return $this->View("setting");
    }
}
