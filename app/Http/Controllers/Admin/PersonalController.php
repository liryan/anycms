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
use App\Models\Category;
use App\Models\DataTable;
use App\Models\ContentTable;
use App\Models\Privileges;
class PersonalController extends AdminController
{
    public function index(Request $req)
    {
        $user=Auth::user();
        $setting=$user->setting;
        $news=[];
        $menus=[];
        $cats=[];
        if($setting){
            $obj=json_decode($setting,true);
            $cate=new Category();
            $dt=new DataTable();
            $content=new ContentTable();   
            foreach($obj['catsub'] as $id){
                $catinfo=$cate->getModelInfo($id);
                $re=$content->getNewCount($catinfo['modelid'],$id);
                $re['name']=$catinfo['name'];
                $news[]=$re;
            }
            $datas=$cate->getDataByIds($obj['catview']);
            foreach($datas as $row){
                $cats[]=['name'=>$row->name,'url'=>"/admin/content?catid=".$row->id];
            }
            $menu_model=new Privileges();  
            $datas=$menu_model->getDataByIds($obj['menuview']);
            foreach($datas as $row){
                $menus[]=['name'=>$row->name,'url'=>$row->note];
            }
        }
        return $this->View("index")->with('cats',$cats)->with('menus',$menus)->with('news',$news);
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

    public function getFetchsetting(Request $req)
    {
        $user=Auth::user();        
        if(!$user){
            return $this->ajax(0,"你需要登录");
        }
        if($user->setting){
            $obj=json_decode($user->setting,true);
            $data=[];
            foreach($obj['catview'] as $id){
                $data['catview_'.$id]=1;
            }
            foreach($obj['catsub'] as $id){
                $data['catsub_'.$id]=1;
            }
            foreach($obj['menuview'] as $id){
                $data['menuview_'.$id]=1;
            }
            return $this->ajax(1,'',$data);
        }
    }

    public function postSavesetting(Request $req)
    {
        $user=Auth::user();        
        if(!$user){
            return $this->ajax(0,"你需要登录");
        }
        $id=$user->id;
        $cat_subids=$this->getValuesByPrefix("catsub_");
        $cat_shortcut=$this->getValuesByPrefix("catview_");
        $menu_shortcut=$this->getValuesByPrefix("menuview_");
        $user_model=new User();
        $setting=Array(
            'catsub'=>$cat_subids,
            'catview'=>$cat_shortcut,
            'menuview'=>$menu_shortcut,
        );
        $re=$user_model->modifyUser($id,Array('setting'=>json_encode($setting)));
        return $this->ajax($re['code'],$re['msg']);
    }

    public function getSetting(Request $req)
    {
        $cate=new Category();
        $pridb=new Privileges();
        $priCats=$cate->getAllCategory();
        $menus=$pridb->getAllMenus();
        $menu_data=[];
        foreach($menus as $row){
            $this->readNode($row,$menu_data,0);
        }
        $re=[];
        foreach($priCats as $row){
            $this->readNode($row,$re,0);
        } 
        
        $view=$this->View("setting");
        return $view->with("cats",$re)
            ->with('menus',$menu_data)
            ->with("get_url",$this->geturl("fetchsetting"))
            ->with("save_url",$this->geturl("savesetting"));
    }

    private function readNode($row,&$re,$deep)
    {
        $re[]=Array('name'=>$row['name'],'note'=>$row['note'],'id'=>$row['id'],'deep'=>$deep);
        if(!isset($row['subdata'])){
            return;
        }
        foreach($row['subdata'] as $node){
            $this->readNode($node,$re,$deep+1);
        }
    }
}
