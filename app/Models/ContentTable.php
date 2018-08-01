<?php

namespace App\Models;
use DB;
use Config;

class ContentTable extends BaseModel
{
	/**
	 * [getList 读取数据列表]
	 * @method getList
	 * @param  [type]  $start        [description]
	 * @param  [type]  $length       [description]
	 * @param  [type]  $table_define [description]
	 * @param  [type]  $id           [description]
	 * @param  [type]  $search       [Array('condition'=>Clouser闭包条件,'order'=>string]
	 * @return [type]                [description]
	 */
	public function getList($start,$length,$table_define,$id,$search=[])
	{
		$field=[];
		$query=null;
		foreach($table_define['columns'] as $row){
			if($row['listable']){
				if(!$query){
					$query=DB::table($table_define['info']['name']);
				}
				$query->addSelect($row['name']);
			}
		}
        try{
            $count=DB::table($table_define['info']['name'])->where($search['condition'])->count();
            $data=$query->where($search['condition'])->orderByRaw($search['order'])->skip($start)->take($length)->get();
            if($data){
                $data=$data->toArray();
            }
            //TODO: modify to left join
            foreach($table_define['columns'] as $row){
                if($row['tablename']){
                    $ids=[];
                    foreach($data as $r){
                        $ids[]=$r->{$row['name']};
                    } 
                    $bind_data=DB::table($row['tablename'])->select($row['tablefield'],$row['tablekey'])->whereIn($row['tablekey'],$ids)->get();
                    $binds=[];
                    foreach($bind_data as $bd){
                        $binds[$bd->{$row['tablekey']}]=$bd->{$row['tablefield']};
                    }
                    foreach($data as &$r){
                        if(isset($binds[$r->{$row['name']}])){
                            $r->{$row['name']}="[".$r->{$row['name']}."]".$binds[$r->{$row['name']}];
                        }
                    }
                }
            }
            return Array('total'=>$count,'data'=>$data);
        }
        catch(\Illuminate\Database\QueryException $e){
            echo $e->getMessage();
            return Array('total'=>0,'data'=>[]);
        }
	}

    public function getNewCount($modelid,$catid)
    {
        $base=new BaseSetting();
        $info=$base->getDataById($modelid);
        $tableName=$info['name'];
        $yd_min=Date('Y-m-d 0:0:0',strtotime('-1 day'));
        $yd_max=Date('Y-m-d 24:0:0',strtotime('-1 day'));

        $td_min=Date('Y-m-d 0:0:0');
        $td_max=Date('Y-m-d 24:0:0');
        $yd_count=DB::table($tableName)
            ->where('category',$catid)
            ->where('created_at','>=',$yd_min)
            ->where('created_at','<',$yd_max)
            ->count();
        $td_count=DB::table($tableName)
            ->where('category',$catid)
            ->where('created_at','>=',$td_min)
            ->where('created_at','<',$td_max)
            ->count();
        return Array('id'=>$catid,'yestoday'=>$yd_count,'today'=>$td_count);
    }

    public function getContent($define,$id)
    {
        $row=DB::table($define['info']['name'])->where('id',$id)->first();
        if($row){
            return json_decode(json_encode($row),true);
        }
        return $row;  
    }

    public function editContent($define,$id,$data,$catid)
    {
        $id=DB::table($define['info']['name'])->where('category',$catid)->where('id',$id)->update($data);     
        return $id;
    }

    public function editContentBatch($define,$ids,$data,$catid)
    {
        $data['updated_at']=date('Y-m-d H:i:s');
        $id=DB::table($define['info']['name'])->where('category',$catid)->whereIn('id',$ids)->update($data);     
        return $id;
    }

    public function addContent($define,$data)
    {
        $id=DB::table($define['info']['name'])->insertGetId($data);     
        return $id>0;
    }

    public function deleteContent($define,$id,$catid)
    {
        return DB::table($define['info']['name'])->where('id',$id)->where('category',$catid)->delete();
    }

    public function deleteContentBatch($define,$ids,$catid)
    {
        return DB::table($define['info']['name'])->where('category',$catid)->whereIn('id',$ids)->delete();
    }

    public function getStatData($condition,$items,$index,$group_date,$table,$month='')
    {
        $part1=[];
        $cols=explode(",",$items);
        $counter=0;
        $header=[];
        foreach($cols as $col){
            $c=explode("as",$col);
            $col_name="item_".$counter++;
            if(count($c)>=2){
                $header[$col_name]=trim($c[1]);
            }
            else{
                $header[$col_name]=$col_name;
            }
            $part1[]=trim($c[0])." as $col_name";
        }
        $tabs=explode(" ",$table);
        $tb=$tabs[0];
        $month=$month?$month:date('Ym');
        $extra="$tb.stat_month=$month";
        $sql=sprintf("select $tb.stat_day,%s from %s where $extra and (%s) group by $tb.stat_day order by $tb.stat_day",implode(",",$part1),$table,$condition);
        $days=DB::select($sql);
        $sql=sprintf("select $tb.stat_month,%s from %s where $extra and (%s) group by $tb.stat_month order by $tb.stat_month",implode(",",$part1),$table,$condition);
        $months=DB::select($sql);
        return Array('header'=>$header,'days'=>$days,'months'=>$months,'month'=>$month);
    }
}
