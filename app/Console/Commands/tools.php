<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Config;
use DB;
class tools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $stdin;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tools set for cms';
    protected $signature = 'tools {cmd} {--mode=0}';
    protected $handlers=[];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private function waitInput($msg)
    {
        $value='';
        do{
            echo "[$msg]:";
            $value=trim(fgets($this->stdin,"256"));
        }while(!$value);
        return $value;
    }

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function when($cmd,$clourse){
        $this->handlers[$cmd]=$clourse;
        return $this;
    }
    private function exec($cmd)
    {
        if(isset($this->handlers[$cmd])){
            $this->handlers[$cmd]();
        }
    }

    public function handle()
    {
        $this->stdin=fopen("php://stdin",'r');
        $cmd=$this->argument('cmd');
        $cls=$this;
        $this
        ->when('reset',function() use($cls){
          $passwd=$cls->waitInput("Please input your new password");
          $user=new User();
          $data=$user->modifyUser(4,Array('role'=>20,'name'=>'admin','password'=>$passwd));
          echo "Modify password successfully";
        })
        ->when('echo',function(){
          echo "only test\n";
        })
        ->when('claw',function() use($cls){
          return $cls->clawPicture();
        })
        ->exec($cmd);
    }

    /**
     * clawPicture
     * 抓取给出字段的内容的所有url到本地,并把url替换
     * select 第一个字段为主键,以后的为要搜索替换的图片的内容字段
     * exp : select id,content from pictures where category = 271
     */
    private function clawPicture()
    {
      $sql = $this->waitInput("Please input SQL,[Exp:select 主键 as id,field1,field2... from tablename where type=1]");
      preg_match("/from[ ]+([^ ]+)/i",$sql,$match);
      $tablename = $match[1];
      $rows = DB::select($sql);
      foreach($rows as $row){
        $data=[];
        $condition='';
        foreach($row as $key=>$value){
          if(!$condition){
            $condition = "`$key`=$value";
            continue;
          }
          $data[$key]=$value;
          $res = preg_match_all("/src[ ]*=[ ]*\"?'?([^'\">]+)/i",$value,$matchs);
          if($res == 0){
            continue;
          }
          $oldv=$value;
          foreach($matchs[1] as $pic){
            if(strpos($pic,"http")!==0){
              continue;
            }
            $content = file_get_contents($pic);
            if($content){
              $root_path = public_path();
              $sub_path = Config::get("UPLOAD_DIR", "/upload");
              $filename = sha1($pic).".jpg";
              $urlpath = "/".trim($sub_path,'/')."/".substr($filename,0,2)."/".substr($filename,2,2)."/".$filename;
              if(!file_exists(dirname($root_path.$urlpath))){
                mkdir(dirname($root_path.$urlpath),0755,true);
              }
              file_put_contents($root_path.$urlpath,$content);
              $value = str_replace($pic,$urlpath,$value);
              echo "has process $pic => $urlpath\n";
              sleep(1);
            }
          }
          if($oldv != $value){
            $data[$key]=$value;
          }
        }
        if($data){
          $sql = "update $tablename set ";
          foreach($data as $k=>$v){
            $sql.="`$k`='".addslashes($v)."',";
          }
          $sql = trim($sql,",")." where $condition";
          DB::update($sql);
          echo "has update $tablename where $condition\n";
        }
      }
    }
}
