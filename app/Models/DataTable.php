<?php

namespace App\Models;
use DB;
use Config;

class DataTable extends BaseSetting
{
    protected const TREE_ID=4;

    public const DEF_INTEGER=1;
    public const DEF_CHAR=2;
    public const DEF_TEXT=3;
    public const DEF_DATE=4;
    public const DEF_LIST=5;
    public const DEF_MULTI_LIST=6;
    public const DEF_IMAGE=7;
    public const DEF_FLOAT=8;
    //字段的类型映射,

    public static $field_type;
    public function __construct()
    {
        if(!static::$field_type){
            static::$field_type=[
                ['name'=>'整数字','value'=>DataTable::DEF_INTEGER,'type'=>'integer','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("integer(%d) default %d",$data['size'],$data['default']);
                    return sprintf("integer(%d)",$data['size']?$data['size']:11);
                }],
                ['name'=>'文本','value'=>DataTable::DEF_CHAR,'type'=>'varchar','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
                    return sprintf("varchar(%d)",$data['size']?$data['size']:255);
                }],
                ['name'=>'编辑器','value'=>DataTable::DEF_TEXT,'type'=>'text','DBdefine'=>function($data){
                    return "text";
                }],
                ['name'=>'日期','value'=>DataTable::DEF_DATE,'type'=>'datetime','DBdefine'=>function($data){
                    return $data['size']==1?'datetime':'date';
                }],
                ['name'=>'选择列表','value'=>DataTable::DEF_LIST,'type'=>'integer','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("integer(11) default %d",$data['default']);
                    return sprintf("integer(11)");
                }],
                ['name'=>'多选列表','value'=>DataTable::DEF_MULTI_LIST,'type'=>'varchar','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
                    return sprintf("varchar(%d)",$data['size']?$data['size']:255);
                }],
                ['name'=>'图片','value'=>DataTable::DEF_IMAGE,'type'=>'varchar','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
                    return sprintf("varchar(%d)",$data['size']?$data['size']:255);
                }],
                ['name'=>'小数点数字','value'=>DataTable::DEF_FLOAT,'type'=>'number','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("decimal(%d,%d) default %.02f",$data['size'],$data['size_bit'],$data['default']);
                    return sprintf("decimal(%d,%d)",$data['size'],$data['size_bit']);
                }],
            ];
        }
    }

    /*****************************************
    * 上面的定义与界面无关
    * ***************************************/

    //视图：模型列表列的展示=>datatable,新模型会根据此表单生成，采用通用表单
    public static $model_fields=[
        ['name'=>'id','label'=>'编号','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
        ['name'=>'note','label'=>'模型名字','place_holder'=>'请输入模型名(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
        ['name'=>'name','label'=>'表名','place_holder'=>'请输入数据表名(字母)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
        ['name'=>'setting','label'=>'备注','place_holder'=>'请输入备注','default'=>'','editable'=>true,'listable'=>false,'type'=>DataTable::DEF_CHAR],
        ['name'=>'created_at','label'=>'创建日期','place_holder'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
        ['name'=>'_internal_field','label'=>'操作','place_holder'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_CHAR]
    ];
    //视图：模型字段列表的数据展现列定义=>datatable,表单为定制表单
    public static $fields_it=[
        ['name'=>'id','label'=>'编号','listable'=>true],
        ['name'=>'note','label'=>'字段名字','listable'=>true],
        ['name'=>'name','label'=>'表字段','listable'=>true],
        ['name'=>'type','label'=>'类型','listable'=>true],
        ['name'=>'created_at','label'=>'创建日期','listable'=>true],
        ['name'=>'_internal_field','label'=>'操作','listable'=>true]
    ];
    //视图：字段的扩展属性定义=>edit setting define
    public static $field_setting=[
        ["name"=>"tablename",'label'=>'关联表名','default'=>''],
        ["name"=>"tablefield",'label'=>'关联表字段','default'=>''],
        ["name"=>"listable",'label'=>'可列表','default'=>'0'],
        ["name"=>"default",'label'=>'缺省值','default'=>''],
        ['name'=>'const','label'=>'选择的常量ID','default'=>''],
        ['name'=>'size','label'=>'字段大小','default'=>''],
        ['name'=>'searchable','label'=>'是否可搜索','default'=>''],
    ];

    //视图+数据库：默认表中会含有以下字段
    public static $default_columns=[
        ['name'=>'id','note'=>'编号','type'=>DataTable::DEF_INTEGER,'def'=>'int(11) auto_increment primary key','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'updated_at','note'=>'更新时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'created_at','note'=>'创建时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>false,'editable'=>false,'searchable'=>true],
        ['name'=>'category','note'=>'栏目','type'=>DataTable::DEF_INTEGER,'def'=>'int(11) default 0','listable'=>false,'editable'=>false,'searchable'=>false]
    ];

	/**
	 * [_tableExist 判断一个表是否存在]
	 * @method _tableExist
	 * @param  [type]      $tablename [description]
	 * @return [type]                 [description]
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
    /**
     * [_fieldExist 判断某个字段是否存在]
     * @method _fieldExist
     * @param  [type]      $tablename [description]
     * @param  [type]      $fieldname [description]
     * @return [type]                 [description]
     */
    private function _fieldExist($tablename,$fieldname)
    {
        $dbname="Tables_in_".Config::get("database.connections.mysql.database");

        $alltable=DB::select("desc $tablename");

        foreach($alltable as $tb){
            if(strcasecmp($tb->Field,$fieldname)==0){
                return true;
            }
        }
        return false;
    }
    /**
     * [tables 获取模型列表]
     * @method tables
     * @param  [type] $start  [description]
     * @param  [type] $length [description]
     * @return [type]         [description]
     */
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

        $count=$this->where('parentid',self::TREE_ID)->where([['id','!=',$id],["note",'=',$data['note']]])->count();
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
            else{
                DB::statement("drop table $tbname");
                $result=$this->deleteData($tableid);
            }
        }
        else{
            $result=$this->deleteData($tableid);
        }
        return true;
	}

	public function addTable($tablename,$data)
	{
        $tablename=strtolower($tablename);
        $data['order']=0;
        $data['name']=$tablename;
        $data['type']=0;

        if($this->_tableExist($tablename)){
            return Array('code'=>0,'msg'=>'表已经存在了');
        }

        $count=$this->where('parentid',self::TREE_ID)->where('name',$tablename)->count();

        if($count>0){
            return Array('code'=>0,'msg'=>'表已经存在了');
        }
        $this->newData(self::TREE_ID,$data);

        $coldef='';
        foreach(static::$default_columns as $col){
            $coldef.=$col['name']." ".$col['def'].",";
        }

        $re=DB::statement("create table $tablename(".trim($coldef,",").")");
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

	public function addField($tableid,$type,$data)
	{
        $fieldname=strtolower($data['name']);
        $data['parentid']=$tableid;
        $parent_table=$this->where('id',$tableid)->first();
        if(!$parent_table){
            return Array('code'=>0,'msg'=>'对应模型不存在');
        }
        if($this->_fieldExist($parent_table->name,$fieldname)){
            return Array('code'=>0,'msg'=>'字段已经存在了');
        }

        $count=$this->where('parentid',$tableid)->where('name',$fieldname)->count();
        if($count>0){
            return Array('code'=>0,'msg'=>'字段已经存在了');
        }

        $this->newData($tableid,$data);
        $re=DB::statement("alter table $parent_table->name add $fieldname $type");
        if(!$re){
            return Array('code'=>0,'msg'=>'创建'.$data['note']."失败了");
        }
        //创建索引:TODO
        return Array('code'=>1,'msg'=>'成功创建'.$data['note']);
	}

	public function deleteField($id)
    {
        $data=$this->where("id",$id)->first();
        if(!$data){
            return false;
        }
        $fieldname=$data->name;
        $data=$this->where("id",$data->parentid)->first();
        if(!$data){
            return false;
        }
        $tbname=$data->name;
        DB::statement("alter table $tbname drop $fieldname");
        $result=$this->deleteData($id);
        return $result>0;
	}
    /**
     * [editField description]
     * @method editField
     * @param  [type]    $id   [字段ID]
     * @param  [type]    $data [Array('name'=>$name,'note'=>$note,'setting'=>$setdata["setting"]]
     * @return [type]          [description]
     */
    public function editField($id,$data)
    {
        if($id<=0){
            return Array('code'=>0,'msg'=>'非法修改');
        }
        $fddata=$this->where("id",$id)->first();
        if(!$fddata){
            return false;
        }
        $fieldname=$fddata->name;
        $tbdata=$this->where("id",$fddata->parentid)->first();
        if(!$tbdata){
            return false;
        }

        $tbname=$tbdata->name;
        DB::statement("alter table $tbname change $fieldname ".$data['name']." ".$data['type']);
        $newdata=[
            'note'=>$data['note'],
            'setting'=>$data['setting'],
            'name'=>$data['name'],
        ];
        $this->editData($id,$newdata);
        return Array('code'=>1,'msg'=>'成功修改'.$data['note']);
    }

    public function tableColumns($modelid)
    {
        $modeldata=$this->getDataById($modelid);
        $data=$this->getDataByParentId($modelid);
        $re=[];
        foreach(static::$default_columns as $row){
            $re[]=['label'=>$row['note'],'name'=>$row['name'],'type'=>$row['type'],'setting'=>'','listable'=>$row['listable'],'editable'=>true,'searchable'=>$row['searchable']];
        }

        if($data){
            foreach($data as $row){
                $obj=json_decode($row['setting'],true);
                $re[]=['label'=>$row['note'],'name'=>$row['name'],'type'=>$row['type'],'listable'=>@$obj['listable'],'setting'=>$obj,'editable'=>true,'searchable'=>@$obj['searchable']];
            }
        }
        $modeldata['columns']=$re;
;
        return $modeldata;
    }
}
