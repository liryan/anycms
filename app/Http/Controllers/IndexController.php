<?php
namespace App\Http\Controllers;
use DB;
use Log;
use Illuminate\Http\Request;
function loginfo($msg){
    \error_log(Date('Ymd H:i:s')."\t".$msg."\n",3,"/tmp/debug.log");
}
class IndexController extends AdminController
{
    public function anyIndex(Request $req)
    {

    }
    public function anyIndex1(Request $req)
    {
        $myage=$req->get('v');

        DB::transaction(function() use($myage){
            loginfo("$myage: start transection get");
            $user=DB::table("member")->where('id',1)->lockForUpdate()->first();
            loginfo("$myage: start sleep");
            sleep(5);
            loginfo("$myage: sleep over");
            $result=DB::table("member")->where('id',1)->update(Array('age'=>$myage));
            loginfo("$myage: sleep modify over");
            if($result==1){
                loginfo("ok");
            }
            else{
                loginfo("failed");
            }
        });
    }
    public function anyTest(){
        $user=DB::table("member")->where('id',2)->lockForUpdate()->first();
        echo $user->age;
    }
}
