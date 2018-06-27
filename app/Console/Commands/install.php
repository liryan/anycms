<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

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
        $this->stdin=fopen("/dev/stdin",'r');
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
            $res=DB::unprepared("create database $dbname");
            DB::unprepared("use $dbname");
            DB::unprepared(file_get_contents($file));
            echo "Congradulation,install successfully\n";
        }
        catch(\PDOException $e){
            echo $e->getMessage()."\n";
        }
    }
}
