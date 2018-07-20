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

    const MENU_USR_ID=10;
    const MENU_SYS_ID=11;

    const ADMIN_ROLE=20;
    private $table_name="t_acl";
    private $priData;
    public static $fields=[
		['name'=>'id','note'=>'编号','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
		['name'=>'name','note'=>'角色名字','comment'=>'请输入(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'created_at','note'=>'创建日期','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
		['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11110','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER]
    ];
    public static $menu_fields=[
		['name'=>'id','note'=>'编号','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
		['name'=>'name','note'=>'菜单','comment'=>'菜单名(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'note','note'=>'URL','comment'=>'请输入URL','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
		['name'=>'created_at','note'=>'创建日期','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
		['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11110','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER]
    ];
    private static $all_privileges=[];

    public function getContentPri($itemid,$admin){
        if($admin==1){
            return "11110";
        }
        if(!isset(self::$all_privileges[$itemid])){
            return "00000";
        }
        return self::$all_privileges[$itemid];
    }
    public function checkPri($itemid,$value,$isAdmin=false)
    {
        if($isAdmin==1){
            return true;
        }
        if(!isset(self::$all_privileges[$itemid])){
            return false;
        }
        $data=self::$all_privileges[$itemid];

        if($value>=self::MAX_PRI || $value<0){
            return false;
        }
        return $data[$value]==1;
    }
    /**
     * function transferForm
     * role define transfrom from DB to form or form to DB
     * @fromDB true:data is from DB,show in form,false:data from from and save to DB
     * @data will fill this object ,that equals return
     */
    public function transferForm(&$data,$key,$value,$fromDB)
    {
        $config=Array(
            "view"=>self::VIEW,
            "add"=>self::ADD,
            "edit"=>self::EDIT,
            "del"=>self::DELETE
        );

        if($fromDB){
            foreach($config as $label=>$bit){
                if(strlen($value) > $bit){
                    $data[$label."_".$key]=$value[$bit];
                }
            }
        }
        else{
            $keys=explode("_",$key);
            if(sizeof($keys)!=2){
                return;
            }
            if(!isset($config[$keys[0]])){
                return;
            }
            $label=$keys[0];
            $bit=$config[$label];
            $id=$keys[1];
            if(!isset($data[$id])){
                $data[$id]="00000";
            }
            $data[$id][$bit]=$value;
        }
    }
    /**
     * load a role's define
     */
    public function loadRoleDefine($roleid)
    {
        $data=[];
        $rows=DB::table($this->table_name)->where("roleid",$roleid)->get();
        if($rows){
            foreach($rows as $role){
                $this->transferForm($data,$role->priitem,$role->value,true);
            }
        }
        return $data;
    }

    /**
     * load one's privileges,this is administrator,not a role
     */
    public function loadUserPri($roles,$cacheData=[])
    {
        if($cacheData){
            static::$all_privileges=$cacheData;
        }
        if($roles){
            $roles=explode(",",$roles);
        }
        else{
            $roles=[];
        }

        $roledata=DB::table($this->table_name)->whereIn('roleid',$roles)->get();
        foreach($roledata as $r){
            static::$all_privileges[$r->priitem]=$r->value;
        }
        return static::$all_privileges;
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
        DB::table($this->table_name)->where('roleid',$id)->delete(); 
        $result=$this->deleteData($id);
        return $result;
    }

    public function getAllMenus()
    {
		return $this->getDataByParentId(self::MENU_USR_ID,true);
    }

    public function getMenus($id,$start,$length)
    {
		if($id==0){
			$id=self::MENU_USR_ID;
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

    public function addMenu($id,$data)
    {
		if($id<=0){
			$id=self::MENU_USR_ID;
		}

		$newdata=[
			'parentid'=>$id,
			'note'=>$data['note'],
			'name'=>$data['name'],
            'setting'=>$data['setting'],
			'order'=>0,
			'type'=>0,
            'updated_at'=>date('Y-m-d H:i:s'),
            'created_at'=>date('Y-m-d H:i:s')
		];

		$count=$this->where('parentid',$id)->where("note",$data['note'])->count();
		if($count>0){
			return Array('code'=>0,'msg'=>'菜单URL已经存在了');
		}

		$this->newData($id,$newdata);
		return Array('code'=>1,'msg'=>'成功添加了'.$newdata['name']);
    }

    public function getIdByUrl($url)
    {
        $data=$this->getDataByParentId(Privileges::MENU_USR_ID,true,function($row) use($url) {
            if($row['note']==$url){
                return true;
            }
        });
        if($data){
            return $data['id'];
        }
        return 0;
    }

    public function editMenu($id,$data)
    {
		if($id<=0){
			return Array('code'=>0,'msg'=>'非法修改');
		}

		$newdata=[
			'name'=>$data['name'],
			'note'=>$data['note'],
            'setting'=>$data['setting'],
            'updated_at'=>date('Y-m-d H:i:s')
		];

		$olddata=$this->where("id",$id)->first();
		$count=$this->where('parentid',$olddata->parentid)->where("note",$data['note'])->count();
		if($count==0){
			return Array('code'=>0,'msg'=>'菜单不存在');
		}

		$this->editData($id,$newdata);
		return Array('code'=>1,'msg'=>'成功修改'.$newdata['name']);
    }

    public function deleteMenu($id,$data)
    {
		$count=$this->where("parentid",$id)->count();
		if($count>0){
			return false;
		}
		$result=$this->deleteData($id);
		return $result>0;
    }
    /**
     * @data Array('itemid'=>'value');
     */
    public function updateRolePri($roleid,$data)
    {
        if($roleid <=0 )
            return false;
        $rows=[];
        foreach($data as $k=>$v){
            $rows[]=['roleid'=>$roleid,'priitem'=>$k,'value'=>$v,'updated_at'=>date('Y-m-d H:i:s'),'created_at'=>date('Y-m-d H:i:s')];
        }
        DB::table($this->table_name)->where('roleid',$roleid)->delete();
        $result=DB::table($this->table_name)->insert($rows);
        return $result;
    }    
}
