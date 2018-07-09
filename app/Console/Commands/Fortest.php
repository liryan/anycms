<?php

namespace App\Console\Commands;
use EasyThumb;
use Illuminate\Console\Command;

class Fortest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url=EasyThumb::from(public_path().'/avatar/avatar.jpg')    //要处理的文件路径
            ->where("/home/ryan/web/cms/anycms/test")
            ->autodir(EasyThumb::SHA1_DIR)
            ->size(100,100,EasyThumb::SCALE_FREE)       //自由缩放  size可以调用多个
            ->size(200,200,EasyThumb::SCALE_PROJECTIVE) //等比缩放
            ->done();
    }
}
