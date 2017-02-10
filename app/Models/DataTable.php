<?php

namespace App\Models;
use DB;
use Config;

class DataTable extends BaseSetting
{
    protected const TREE_ID=4;
	/**
	*
	*/
    private function _tableExist($tablename)
    {
        $dbname="Tables_in_".Config::get("database.connections.mysql.database");

        $alltable=DB::select("show tables");

        foreach($alltable as $tb){
            if(strcasecmp($tb->$dbname,$tablename)==0){
                return true;
            }
        }
        return false;
    }

    private function _fieldExist($tablename)
    {
        $dbname="Tables_in_".Config::get("database.connections.mysql.database");

        $alltable=DB::select("desc tables");

        foreach($alltable as $tb){
            if(strcasecmp($tb->$dbname,$tablename)==0){
                return true;
            }
        }
        return false;
    }

	public function tables($start,$length)
	{
		if($start<0)
			$start=0;
		if($length<1){
			$length=20;
		}
        $total=$this->getCount(self::TREE_ID);
		$data=$this->getDataPageByParentId(self::TREE_ID,$start,$length);
        return Array('total'=>$total,'data'=>$data);
	}

    public function getTableById($id)
    {
        return $this->getDataById($id);
    }


    public function editTable($id,$data)
    {
        if($id<=0){
            return Array('code'=>0,'msg'=>'非法修改');
        }

        $newdata=[
            'note'=>$data['note'],
            'setting'=>$data['setting']
        ];

        $count=$this->where('parentid',self::TREE_ID)->where([['id','!=',$id],["note",'!=',$data['note']]])->count();
        if($count>0){
            return Array('code'=>0,'msg'=>'模型已经存在了');
        }
        $this->editData($id,$newdata);
        return Array('code'=>1,'msg'=>'成功修改'.$data['note']);
    }

    public function deleteTable($tableid)
	{
        $data=$this->where("id",$tableid)->first();
        if(!$data){
            return false;
        }

        $tbname=$data->name;

        if($this->_tableExist($tbname)){
            $count=DB::table($tbname)->count();
            if($count>0){
                return false;
            }
        }
        DB::statement("drop table $tbname");
        $result=$this->deleteData($tableid);
        return $result>0;
	}

	public function addTable($tablename,$data)
	{
        $tablename=strtolower($tablename);
        $data['order']=0;
        $data['name']=$tablename;

        if($this->_tableExist($tablename)){
            return Array('code'=>0,'msg'=>'表已经存在了');
        }

        $count=$this->where('parentid',self::TREE_ID)->where('name','$tablename')->count();

        if($count>0){
            return Array('code'=>0,'msg'=>'表已经存在了');
        }
        $this->newData(self::TREE_ID,$data);
        $re=DB::statement("create table $tablename(id int(11) auto_increment primary key,updated_at datetime not null,created_at datetime not null,category int(11) default 0)");
        if(!$re){
            return Array('code'=>0,'msg'=>'创建'.$data['note']."失败了");
        }
        //创建索引:TODO
        return Array('code'=>1,'msg'=>'成功创建'.$data['note']);
	}

    /**
     * 获取一个表的所有字段
     * @method fields
     * @param  [type] $id     [表ID]
     * @param  [type] $start  [description]
     * @param  [type] $length [description]
     * @return [type]         [description]
     */
    public function fields($id,$start,$length)
	{
		if($start<0)
			$start=0;
		if($length<1){
			$length=20;
		}
        $total=$this->getCount($id);
		$data=$this->getDataPageByParentId($id,$start,$length);
        return Array('total'=>$total,'data'=>$data);
	}

	public function addField($tableid,$name,$label,$type,$default)
	{

	}

	public function deleteField($tableid,$id){

	}
}
