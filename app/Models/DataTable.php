<?php

namespace App\Models;
use DB;
use Config;

class DataTable extends BaseSetting
{
    const TREE_ID=4;

    const DEF_INTEGER=1;
    const DEF_CHAR=2;
    const DEF_TEXT=3;
    const DEF_DATE=4;
    const DEF_LIST=5;
    const DEF_MULTI_LIST=6;
    const DEF_IMAGE=7;
    const DEF_FLOAT=8;
    const DEF_EDITOR=9;
    const DEF_IMAGES=10;
    //字段的类型映射,

    private static $field_type;  //模型表的字段类型
    public static function field_type()
    {
        if(!static::$field_type){
            static::$field_type=[
                ['name'=>'整数字','value'=>DataTable::DEF_INTEGER,'type'=>'integer','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("integer(%d) default %d",$data['size'],$data['default']);
                    return sprintf("integer(%d)",$data['size']?$data['size']:11);
                }],
                ['name'=>'字符串','value'=>DataTable::DEF_CHAR,'type'=>'varchar','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("varchar(%d) default '%s'",$data['size'],$data['default']);
                    return sprintf("varchar(%d)",$data['size']?$data['size']:255);
                }],
                ['name'=>'文本','value'=>DataTable::DEF_TEXT,'type'=>'text','DBdefine'=>function($data){
                    return "text";
                }],
                ['name'=>'编辑器','value'=>DataTable::DEF_EDITOR,'type'=>'text','DBdefine'=>function($data){
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
                ['name'=>'多图片','value'=>DataTable::DEF_IMAGES,'type'=>'text','DBdefine'=>function($data){
                    return sprintf("text");
                }],
                ['name'=>'小数点数字','value'=>DataTable::DEF_FLOAT,'type'=>'number','DBdefine'=>function($data){
                    if(strlen($data['default'])>0)
                        return sprintf("decimal(%d,%d) default %.02f",$data['size'],$data['size_bit'],$data['default']);
                    return sprintf("decimal(%d,%d)",$data['size'],$data['size_bit']);
                }],
            ];
        }
        return static::$field_type;
    }

    /*****************************************
    * 上面的定义与界面无关
    * ***************************************/

    //视图（列表页）：模型列表展示=>datatable,展示这些数据，同时，新模型弹出页面也会根据此表单生成，采用通用表单
    public static $model_fields=[
        ['name'=>'id','note'=>'编号','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_INTEGER],
        ['name'=>'note','note'=>'模型名字','comment'=>'请输入模型名(中文)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
        ['name'=>'name','note'=>'表名','comment'=>'请输入数据表名(字母)','default'=>'','editable'=>true,'listable'=>true,'type'=>DataTable::DEF_CHAR],
        ['name'=>'setting','note'=>'备注','comment'=>'请输入备注','default'=>'','editable'=>true,'listable'=>false,'type'=>DataTable::DEF_CHAR],
        ['name'=>'created_at','note'=>'创建日期','comment'=>'','default'=>'','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_DATE],
        ['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11111','editable'=>false,'listable'=>true,'type'=>DataTable::DEF_CHAR]
    ];
    //视图（列表页）：模型字段列表的数据展现列定义=>datatable,表单为定制表单
    public static $fields_it=[
        ['name'=>'id','note'=>'编号','listable'=>true],
        ['name'=>'note','note'=>'字段名字','listable'=>true],
        ['name'=>'name','note'=>'表字段','listable'=>true],
        ['name'=>'type','note'=>'类型','listable'=>true],
        ['name'=>'created_at','note'=>'创建日期','listable'=>true],
        ['name'=>'_internal_field','note'=>'操作','listable'=>true]
    ];
    //视图：字段的扩展属性定义=>edit setting define
    public static $field_setting=[
        ["name"=>"tablename",'note'=>'关联表名','default'=>''],
        ["name"=>"tablefield",'note'=>'关联表字段','default'=>''],
        ["name"=>"editable",'note'=>'可编辑','default'=>'1'],
        ["name"=>"listable",'note'=>'可列表','default'=>'0'],
        ["name"=>"batchable",'note'=>'可批量修改','default'=>'0'],
        ["name"=>"searchable",'note'=>'可搜索','default'=>'0'],
        ["name"=>"default",'note'=>'缺省值','default'=>''],
        ['name'=>'const','note'=>'选择的常量ID','default'=>''],
        ['name'=>'size','note'=>'字段大小','default'=>''],
        ['name'=>'comment','note'=>'字段提示','default'=>''],
        ['name'=>'format','note'=>'格式说明','default'=>''],
    ];

    //视图+数据库：默认表中会含有以下字段（获取表的定义的时候，会把这些字段合并到定义中，因为这些字段不出现在模型定义中）
    public static $default_columns=[
        ['name'=>'id','note'=>'编号','type'=>DataTable::DEF_INTEGER,'def'=>'int(11) auto_increment primary key','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'updated_at','note'=>'更新时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'created_at','note'=>'创建时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>false,'editable'=>false,'searchable'=>true],
        ['name'=>'category','note'=>'栏目','type'=>DataTable::DEF_INTEGER,'def'=>'int(11) default 0','listable'=>false,'editable'=>false,'searchable'=>false],
    ];

    public function fillDefault(&$row)
    {
        $row['updated_at']=date('Y-m-d H:i:s');
        $row['created_at']=date('Y-m-d H:i:s');
    }

    public function filterForEdit(&$row){
        unset($row['id']);
        unset($row['created_at']);
    }

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

        $alltable=DB::select("desc `$tablename`");

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
                DB::statement("drop table `$tbname`");
                $result=$this->deleteData($tableid,true);
            }
        }
        else{
            $result=$this->deleteData($tableid,true);
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

        $re=DB::statement("create table `$tablename`(".trim($coldef,",").")");
        if(!$re){
            return Array('code'=>0,'msg'=>'创建'.$data['note']."失败了");
        }
        //创建索引:TODO
        return Array('code'=>1,'msg'=>'成功创建'.$data['note']);
	}

    /**
     * 获取一个表的所有字段，分页
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


    /**
     * 新增字段
     * @method addField
     * @param  [type]   $tableid [description]
     * @param  [type]   $type    [description]
     * @param  [type]   $data    [description]
     */
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
        $re=DB::statement("alter table `$parent_table->name` add `$fieldname` $type");
        if(!$re){
            return Array('code'=>0,'msg'=>'创建'.$data['note']."失败了");
        }
        //创建索引:TODO
        return Array('code'=>1,'msg'=>'成功创建'.$data['note']);
	}

    /**
     * 删除字段
     * @method deleteField
     * @param  [type]      $id [description]
     * @return [type]          [description]
     */
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
        try{
            DB::statement("alter table `$tbname` drop `$fieldname`");
        }
        catch(\Illuminate\Database\QueryException $e){
        }
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
        DB::statement("alter table `$tbname` change `$fieldname` ".$data['name']." ".$data['type']);
        $newdata=[
            'note'=>$data['note'],
            'setting'=>$data['setting'],
            'name'=>$data['name'],
        ];
        $this->editData($id,$newdata);
        return Array('code'=>1,'msg'=>'成功修改'.$data['note']);
    }
    /**
     * 获取一个字段定义的扩展类型字段，读取setting字段
     * @method readSetting
     * @param  [type]      $data [description]
     * @return [type]            [description]
     */
    public function readSetting(&$data)
    {
        if(isset($data['setting']) && $data['setting'] ){
            $obj=json_decode($data['setting'],true);
        }
        else{
            $obj=$data;
        }
        foreach(static::$field_setting as $row){
            if(isset($obj[$row['name']])){
                $data[$row['name']]=$obj[$row['name']];
            }
            else{
                $data[$row['name']]=$row['default'];
            }
        }
    }
    /**
     * [获取一个表的所有字段定义]
     * @method tableColumns
     * @param [type]        $includeDefault  是否包含默认字段
     * @param  [type]       $modelid [description]
     * @return [type]                [description]
     */
    public function tableColumns($modelid,$includeDefault=true)
    {
        $modeldata=$this->getDataById($modelid); //获取模型定义
        $data=$this->getDataByParentId($modelid);//获取字段定义
        $re=[];
        if($includeDefault){
            foreach(static::$default_columns as $row){
                $this->readSetting($row);
                $re[]=$row;
            }
        }

        if($data){
            foreach($data as $row){
                $this->readSetting($row);
                $re[]=$row;
            }
        }

        return ['info'=>$modeldata,'columns'=>$re];
    }
}
