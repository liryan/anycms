<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\User;

class install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install {sqlf=./database/admlite.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Admlite system';

    protected $stdin;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * waitInput 
     * 
     * @param mixed $msg promot
     * @param mixed $value 
     * @access private
     * @return string
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

    public function handle()
    {
        $this->stdin=fopen("php://stdin",'r');

        $file=$this->argument('sqlf');
        if(!file_exists($file)){
            echo "[$file] doesn't exist\n";
            echo "Install Exit\n";
            return;
        }
        echo "Welcome Install interface\n";
        $dbname=$this->waitInput("Please input your [Database Name]");
        DB::unprepared("set names utf8");
        try{
            DB::unprepared("use $dbname");
            DB::unprepared(file_get_contents($file));
            $email=$this->waitInput('输入管理员邮箱:'); 
            $password=$this->waitInput('输入管理员密码,至少6个字符:'); 
            $user=new User();
            $data=$user->modifyUser(0,Array('role'=>20,'name'=>'admin','password'=>$password,'email'=>$email));
            if($data['code']==1){
                echo "Congradulation,install successfully\n";
            }
            else{
                echo "install failed\n";
            }
        }
        catch(\PDOException $e){
            echo $e->getMessage()."\n";
        }
    }
}
