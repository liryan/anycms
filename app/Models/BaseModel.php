<?php
namespace App\Models;
use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $debugFlag;
	public function __construct()
	{
		DB::connection()->enableQueryLog();
	}
	public function __destruct()
	{
		Log::info(DB::connection()->getQueryLog());
	}
}
