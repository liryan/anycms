<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\User;
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
    protected $signature = 'tools {cmd}';
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
    public function done($cmd)
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
            $data=$user->modifyUser(3,Array('role'=>20,'name'=>'admin1','password'=>$passwd));
            echo "Modify password successfully";
        })
        ->when('echo',function(){
            echo "only test\n";
        })
        ->done($cmd);
    }
}
