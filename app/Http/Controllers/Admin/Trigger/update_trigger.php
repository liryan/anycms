<?php
/**
 * 删除触发器，一旦某条数据被更新，将会调用该数据表的category字段对应值的函数
 *
 function after_edit_{catid}($id,array $data,array $new)
 {
   DB::select("select count(*) from tablename");
 }
*/
function after_edit_282($id,$data,$new)
{
}
