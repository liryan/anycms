<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Log;

/**
 * the base class of model
 * @author liruiyan <canbetter@163.com>
 */
class BaseModel extends Model
{
    protected $debugFlag;
    public function __construct()
    {
        //DB::connection()->enableQueryLog();
    }
    public function __destruct()
    {
        //Log::info(DB::connection()->getQueryLog());
    }
}
