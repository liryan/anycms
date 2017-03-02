<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class fetch_tangshi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $tag=[
        "写景",
        "咏物",
        "春天",
        "夏天",
        "秋天",
        "冬天",
        "写雨",
        "写雪",
        "写风",
        "写花",
        "梅花",
        "荷花",
        "菊花",
        "柳树",
        "月亮",
        "山水",
        "写山",
        "写水",
        "长江",
        "黄河",
        "儿童",
        "写鸟",
        "写马",
        "田园",
        "边塞",
        "地名",
        "抒情",
        "爱国",
        "离别",
        "送别",
        "思乡",
        "思念",
        "爱情",
        "励志",
        "哲理",
        "闺怨",
        "悼亡",
        "写人",
        "老师",
        "母亲",
        "友情",
        "战争",
        "读书",
        "惜时",
        "婉约",
        "豪放",
        "诗经",
        "民谣",
        "节日",
        "春节",
        "元宵节",
        "寒食节",
        "清明节",
        "端午节",
        "七夕节",
        "中秋节",
        "重阳节",
        "忧国忧民",
        "咏史怀古",
        "宋词精选",
        "小学古诗",
        "初中古诗",
        "高中古诗",
        "小学文言文",
        "初中文言文",
        "高中文言文",
        "古诗十九首",
        "唐诗三百首",
        "古诗三百首",
        "宋词三百首",
        "古文观止",
    ];

    protected $age=[
        "先秦",
        "两汉",
        "魏晋",
        "南北朝",
        "隋代",
        "唐代",
        "五代",
        "宋代",
        "金朝",
        "元代",
        "明代",
        "清代",
    ];

    protected $type=[
        "诗",
        "词",
        "曲",
    ];

    protected $url="http://so.gushiwen.org/type.aspx?p=%d&t=%s&c=%s&x=%s";
    protected $signature = 'command:fetch_ts';

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

    public function tlog($msg,$lineend=0)
    {
        if($lineend===2){
            echo "$msg\n";
        }
        else if($lineend===0){
            echo date('Y-m-d H:i:s')."\t".$msg."\n";
        }
        else if($lineend===1){
            echo date('Y-m-d H:i:s')."\t".$msg;
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function fetch_list_url()
    {
        $logfile="/tmp/fetch_log.txt";
        do{
            $obj=false;
            if(file_exists($logfile)){
                $obj=unserialize(file_get_contents($logfile));
            }

            if(!$obj){
                $obj=['type'=>0,'page'=>1,'tag'=>0,'age'=>0];
            }
            $type=$this->type[$obj['type']];
            $tag=$this->tag[$obj['tag']];
            $age=$this->age[$obj['age']];
            $page=$obj['page'];
            $url=sprintf($this->url,$page,$tag,$age,$type);
            $this->tlog("fetch $url",1);
            $result=$this->parse_list($url);
            $this->tlog(json_encode($obj));
            if(!$result){
                $this->tlog("page finished",2);
                $obj['page']=1;
                $obj['tag']++;
                if($obj['tag']>=count($this->tag)){
                    $obj['tag']=0;
                    $obj['age']++;
                    if($obj['age']>=count($this->age)){
                        $obj['age']=0;
                        $obj['type']++;
                        if($obj['type']>=count($this->type)){
                            return false;
                        }
                    }
                }
            }
            else{
                $obj['page']++;
                $this->tlog("next page",2);
            }
            file_put_contents($logfile,serialize($obj));
        }while(1);
    }
    protected function test_url(){
        //file_put_contents("/tmp/txt.txt",file_get_contents('http://so.gushiwen.org/type.aspx?p=1&t=写景&c=唐代&x=诗'));
    }
    protected function parse_list($url)
    {
        //$content=file_get_contents($url);
        $content=file_get_contents("/tmp/txt.txt");
        if(preg_match_all('/14px;" href="(\/view_[0-9]+\.aspx)/i',$content,$ma)){
            if($ma[1]){
                return false;
            }
            foreach($ma[1] as $detail_url){
                $this->parse_content(file_get_contents($detail_url));
            }
        }
        else{
            return false;
        }
    }

    protected function parse_content($content)
    {
        $age='';
        $author='';
        $text='';
        if(preg_match('/<span>朝代：</span>([^<]+)</i',$content,$ma)){
            $age=$ma[1];
        }
        if(preg_match('/<span>作者：</span>([^<]+)<\/a/i',$content,$ma)){
            $author=$ma[1];
        }
        if(preg_match('/<span>原文：</span><\/p>([^<]+)<\/div/i',$content,$ma)){
            $text=$ma[1];
        }
        $yiwen=[];
        if(preg_match('/\/fanyi_[0-9]+\.aspx/i',$content,$ma)){
            if(!is_array($ma[0])){
                $ma[0]=[$ma[0]];
            }
            foreach($ma[0] as $url){
                $yiwen[]=$this->parse_fanyi("http://so.gushiwen.org".$url);
            }
        }
        $shangxi=[];
        if(preg_match('/\/shangxi_[0-9]+\.aspx/i',$content,$ma)){
            if(!is_array($ma[0])){
                $ma[0]=[$ma[0]];
            }
            foreach($ma[0] as $url){
                $shangxi[]=$this->parse_xiangxi("http://so.gushiwen.org".$url);
            }
        }
        $author_text='';
        if(preg_match('/\/author_[0-9]+\.aspx/i',$content,$ma)){
            $author_text[]=$this->parse_author("http://so.gushiwen.org".$url);
        }
    }

    protected function parse_xiangxi($url)
    {
        //$content=file_get_contents($url);
    }

    protected function parse_fanyi($url)
    {
        //$content=file_get_contents($url);
    }

    protected function parse_author($url)
    {
        //$content=file_get_contents($url);
    }

    public function handle()
    {
        $this->test_url();
        $this->tlog("task is start",0);
        $this->parse_list('');
        //$this->fetch_list_url();
        $this->tlog("task is over",0);
    }
}
