<?php

namespace App\Models;
use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    //缺省的管理员表的模型定义
    public static $name="管理员";
    public static $table_name="t_admin";

    public static $field_setting=[
        ["name"=>"role",'note'=>'角色','default'=>'','setting'=>''],
        ["name"=>"status",'note'=>'状态','default'=>'','setting'=>'']
    ];
    public static $admin_fields=[
        ['name'=>'id','note'=>'编号','type'=>DataTable::DEF_INTEGER,'def'=>'int(11) auto_increment primary key','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'updated_at','note'=>'更新时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'created_at','note'=>'创建时间','type'=>DataTable::DEF_DATE,'def'=>'datetime not null','listable'=>false,'editable'=>false,'searchable'=>true],
        ['name'=>'email','note'=>'账号','type'=>DataTable::DEF_CHAR,'def'=>'varchar(64)','listable'=>true,'editable'=>false,'searchable'=>true],
        ['name'=>'name','note'=>'名字','type'=>DataTable::DEF_CHAR,'def'=>'varchar(32)','listable'=>true,'editable'=>true,'searchable'=>true],
        ['name'=>'role','note'=>'角色','type'=>DataTable::DEF_LIST,'def'=>'varchar(256)','listable'=>true,'editable'=>true,'searchable'=>false],
        ['name'=>'password','note'=>'密码','type'=>DataTable::DEF_CHAR,'def'=>'varchar(64)','listable'=>false,'editable'=>true,'searchable'=>false],
        ['name'=>'status','note'=>'状态','type'=>DataTable::DEF_LIST,'def'=>'tinyint(4) default 1','listable'=>true,'editable'=>true,'searchable'=>false],
		['name'=>'_internal_field','note'=>'操作','comment'=>'','default'=>'11111','editable'=>false,'listable'=>false,'type'=>DataTable::DEF_INTEGER]
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getList($start,$length)
    {
        $table_define=['name'=>self::$table_name,'columns'=>self::$admin_fields];
		$field=[];
		$query=null;
		foreach($table_define['columns'] as $row){
			if($row['listable']){
			    $field[]=$row['name'];
			}
		}

        try{
            $data=DB::table(self::$table_name)->select($field)->orderByRaw("id asc")->skip($start)->take($length)->get();
            $count=DB::table(self::$table_name)->count();
            if($data){
                $data=$data->toArray();
            }

            return Array('total'=>$count,'data'=>$data);
        }
        catch(\Illuminate\Database\QueryException $e){
            echo $e->getMessage();
            return Array('total'=>0,'data'=>[]);
        }
    }
}
