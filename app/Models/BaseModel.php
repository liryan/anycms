<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	public function __construct()
	{
		DB::connection()->enableQueryLog();
	}
}
