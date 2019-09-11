<?php
/**
 * 删除触发器，一旦某条数据被删除，将会调用该数据表的category字段对应值的函数
 function after_delete_{catid}($id)
 {
   DB::select("select count(*) from tablename");
 }
*/
 function after_delete_282($id)
 {
 }
