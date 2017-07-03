<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class Privileges extends BaseSetting{
    const VIEW=0;
    const ADD=1;
    const EDIT=2;
    const DELETE=3;
    const MAX_PRI=4;

    const ROLE_ID=5;
    const MENU_ID=2;

    private $priData;
    public static $fields=[
		['name'=>'id','note'=>'编号','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
		['name'=>'name','note'=>'角色名字','comment'=>'请输入(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'created_at','note'=>'创建日期','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
		['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11110','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER]
    ];
    /**
     * priData=>Array('pri'=>1111,tablename=>'table',fields=>['fieldname1'=>1111,'fieldname2=>1111])
     */
    private static $all_privileges=[];

    public function init($data)
    {
        $this->priData=json_decode($data,true);
    }

    /**
     * filterField 
     * remove the rows that no privlege to operate 
     * @param mixed $oppri 
     * @param mixed $data 
     * @access public
     * @return void
     */
    public function filterField($oppri,$data)
    {
        if($this->priData && isset($this->priData['fields']) && $this->priData['fields']){
            $result=[];
            foreach($data as $k=>$v){
                if(isset($this->priData['fields'][$k])){
                    $pri=$this->priData['fields'][$k];   
                    if($this->checkPri($oppri,$pri)){
                        $result[$k]=$v;
                    }
                }
            }
            return $result;
        }
        return $data;
    } 

    private function checkPri($pri,$data)
    {
        if($pri>=self::$MAX_PRI || $pri<0){
            return false;
        }

        if($data){
            return $data[$pri]==1;
        }
        return false;
    }

    public function checkMenuPri($pri)
    {
        return $this->checkPri($pri,$this->priData['pri']);
    }

    public static function loadData($userid)
    {
        $userdata=DB::table('t_admin')->where('id',$userid)->first();
        $roles=$userdata->roles;
        if($roles){
            $roles=explode(",",$roles);
        }
        else{
            $roles=[];
        }
        foreach($roles as $role){
            $roledata=DB::table('t_acl')->whereIn('id',$role)->get();
            $pri_ids=[];
            foreach($roledata as $r){
                $pri_ids[]=$r->priid;
            }
            $pri=DB::table('setting')->whereIn('id',$pri_ids)->get();
            foreach($pri as $p){
                $setting=json_decode($p->setting,true);
                //$setting=Array('menuid'=>1,'pridata'=>Array('pri'=>1111,fields=>['fieldname'=>11111,....]));
                static::$all_privileges[$setting['menuid']]=$setting['pridata'];
            }
        }
    }

    public function getRoles($start,$length,$id=0)
    {
		if($id==0){
			$id=self::ROLE_ID;
		}
		if($start<0)
			$start=0;
		if($length<1){
			$length=20;
		}
		$total=$this->getCount($id);
		$data=$this->getDataPageByParentId($id,$start,$length);
		return Array('total'=>$total,'data'=>$data);
    }

    public function addRole($data)
    {
        $setdata=[];
        $setdata['name']=$data['name'];
        $setdata['setting']='';
        $setdata['order']=0;
        $setdata['type']=0;
        $setdata['note']='';
        $re=$this->newData(self::ROLE_ID,$setdata);
		return Array('code'=>1,'msg'=>'成功添加'.$data['name']);
    }

    public function editRole($id,$data)
    {
		if($id<=0){
			return Array('code'=>0,'msg'=>'非法修改');
		}
        $setdata=[];
        $setdata['name']=$data['name'];
        $setdata['setting']='';
        $setdata['order']=0;
        $setdata['type']=0;
        $setdata['note']='';
        $this->editData($id,$setdata);
		return Array('code'=>1,'msg'=>'成功修改'.$data['name']);
    }

    public function deleteRole($id)
    {
        DB::table('t_acl')->where('roleid',$id)->delete(); 
        $result=$this->deleteData($id);
        return $result;
    }

    public function getMenus($id,$start,$length)
    {
		if($id==0){
			$id=self::MENU_ID;
		}
		if($start<0)
			$start=0;
		if($length<1){
			$length=20;
		}
		$total=$this->getCount($id);
		$data=$this->getDataPageByParentId($id,$start,$length);
		return Array('total'=>$total,'data'=>$data);
    }

    public function addMenu($parentid,$data)
    {
          
    }

    public function editMenu($id,$data)
    {

    }

    public function deleteMenu($id)
    {

    }

    public function addPri()
    {

    }    

    public function editPri()
    {
    }    

    public function deletePri()
    {
    }    

}
