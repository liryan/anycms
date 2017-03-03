<?php

namespace App\Console\Commands;
use DB;
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
            $result=$this->parse_list($url,['tag'=>$tag,'age'=>$age,'type'=>$type]);
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
    protected function fetchUrl($url)
    {
        sleep(1);
        return file_get_contents($url);
    }
    protected function parse_list($url,$context)
    {
        //$content=$this->fetchUrl($url);
        $content=file_get_contents("/tmp/txt.txt");
        if(preg_match_all('/14px;" href="(\/view_[0-9]+\.aspx)[^>]+>([^<]+)</i',$content,$ma)){
            if(!$ma[1]){
                return false;
            }
            $tx=[];
            foreach($ma[1] as $k=>$detail_url){
                $tx['title']=$ma[2][$k];
                $tx['age']=$this->age[$context['age']];
                $tx['type']=$this->type[$context['type']];
                $tx['tag']=$this->tag[$context['tag']];
                $this->parse_content($this->fetchUrl("http://so.gushiwen.org".$detail_url),$tx);
                die();
            }
        }
        else{
            return false;
        }
    }

    protected function parse_content($content,$tx)
    {
        $age='';
        $author='';
        $text='';
        if(preg_match('/<span[^>]*>朝代：<\/span>([^<]+)</i',$content,$ma)){
            $age=$ma[1];
        }
        if(preg_match('/<span[^>]*>作者：<\/span><a [^>]+>([^<]+)</i',$content,$ma)){
            $author=$ma[1];
        }
        if(preg_match('/<div class=\"cont\" id=\"cont\">:?([\s\S]+?)<\/div/i',$content,$ma)){
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
            $url=$ma[0];
            $author_text=$this->parse_author("http://so.gushiwen.org".$url);
        }
        $data=[];
        $data['age']=$this->filter_tag($age);
        $data['author']=$this->filter_tag($author);
        $data['text']=$this->filter_tag($text);
        $data['yiwen']=[];
        $data['shangxi']=[];
        foreach($yiwen as $row){
            $data['yiwen'][]=$this->filter_tag($row);
        }
        foreach($shangxi as $row){
            $data['shangxi'][]=$this->filter_tag($row);
        }
        $data['author_info']=$this->filter_tag($author_text);
        $this->save_db(array_merge($tx,$data));
    }

    protected function save_db($data)
    {
        $row=Array(
            'name'=>$data['author'],
            'age'=>$data['age'],
            'text'=>$data['author_info'],
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
        );
        $old=DB::table('t_authorinfo')->where('age',$data['age'])->where('name',$data['author'])->first();
        $authorid=0;
        if($old){
            $authorid=$old->id;
        }
        else
            $authorid=DB::table("t_authorinfo")->insertGetId($row);
        if($authorid){
            $row=Array(
                'title'=>$data['title'],
                'text'=>$data['text'],
                'author'=>$data['author'],
                'authorid'=>$authorid,
                'age'=>$data['age'],
                'tag'=>$data['tag'],
                'type'=>$data['type'],
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'keyword'=>$this->make_keyword($data['author'].trim($data['title']).$data['age'].$data['type'].$data['tag'].$data['text']),
            );
            $old=DB::table('t_content')->where('authorid',$authorid)->where('title',$data['title'])->first();
            if($old){
                return;
            }
            $contentid=DB::table("t_content")->insertGetId($row);
            foreach($data['yiwen'] as $txt){
                $row=[
                    'contentid'=>$contentid,
                    'text'=>$txt,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
                $contentid=DB::table("t_comment")->insertGetId($row);
            }

            foreach($data['shangxi'] as $txt){
                $row=[
                    'contentid'=>$contentid,
                    'text'=>$txt,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
                $contentid=DB::table("t_note")->insertGetId($row);
            }
        }
    }

    protected function make_keyword($keyword)
    {
        $keyword=preg_replace("/[a-z<>\/!@#$%^&*(_+=) ]+/i",'',$keyword);
        $result='';
        $sh=scws_open();
        scws_set_charset($sh,'utf8');
        scws_set_dict($sh,'/usr/local/scws/etc/dict.utf8.xdb');
        scws_set_rule($sh,'/usr/local/scws/etc/rules.utf8.ini');
        scws_set_ignore($sh,true);
        scws_set_multi($sh,true);
        scws_set_duality($sh,true);
        $top=scws_send_text($sh,$keyword);
        $ar=scws_get_result($sh);
        foreach($ar as $row){
            $result.=$row['word']." ";
        }
        return trim($result);
    }

    protected function filter_tag($content)
    {
        $content= preg_replace("/<([a-z]+) ?[^>]+>/i","<$1>",$content);
        $remove=["<div>","</div>","<img>","<a>","</a>","下载","纠错","加资料","站务邮箱：service@gushiwen.org","gushiwen.org","古诗文网"];
        foreach($remove as $k){
            $content=str_replace($k,"",$content);
        }
        return trim($content);
    }

    protected function parse_xiangxi($url)
    {
        $content=$this->fetchUrl($url);
        if(preg_match("/<div class=\"shangxicont\">([\s\S]+)<div class=\"youorno\">/i",$content,$ma)){
            return $ma[1];
        }
        return "";
    }

    protected function parse_fanyi($url)
    {
        $content=$this->fetchUrl($url);
        if(preg_match("/<div class=\"shangxicont\">([\s\S]+)<div class=\"youorno\">/i",$content,$ma)){
            return $ma[1];
        }
        return "";
    }

    protected function parse_author($url)
    {
        $content=$this->fetchUrl($url);
        if(preg_match("/<div class=\"son2\" style=\"overflow:auto;\">([\s\S]+)<div class=\"shoucang\">/i",$content,$ma)){
            return $ma[1];
        }
        return "";
    }

    public function handle()
    {
        $this->test_url();
        $this->tlog("task is start",0);
        $this->parse_list('',['tag'=>0,'age'=>0,'type'=>0]);
        //$this->fetch_list_url();
        $this->tlog("task is over",0);
    }
}
