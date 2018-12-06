<?php

namespace App\Models;

use DB;

class ContentTable extends BaseModel
{
    private function defaultCondition()
    {
        return [
            'condition' => function ($query) {
                $query->where('id', '>', 0);
            },
            'order' => "id desc",
        ];
    }
    /**
     * [getList 读取数据列表]
     * @method getList
     * @param  [int]    $start        [begin of the row]
     * @param  [int]    $length       [count of per page]
     * @param  [array]  $table_define [description of model]
     * @param  [int]    $id           [the id of category]
     * @param  [array]  $search       [Array('condition'=>Clouser闭包，返回一个条件array,'order'=>string]
     * @return [array]                [see function]
     */
    public function getList($start, $length, $table_define, $id, $search = [])
    {
        DB::enableQueryLog();
        $field = [];
        $query = null;
        foreach ($table_define['columns'] as $row) {
            if ($row['listable']) {
                if (!$query) {
                    $query = DB::table($table_define['info']['name']);
                }
                $query->addSelect($row['name']);
            }
        }
        try {
            if (!$search) {
                $search = $this->defaultCondition();
            }
            $count = DB::table($table_define['info']['name'])->where("category", $id)->where($search['condition'])->count();
            $data = $query->where("category", $id)->where($search['condition'])->orderByRaw($search['order'])->skip($start)->take($length)->get();
            if ($data) {
                $data = $data->toArray();
            }
            //TODO: modify to left join
            foreach ($table_define['columns'] as $row) {
                if ($row['tablename']) {
                    $ids = [];
                    foreach ($data as $r) {
                        $ids[] = $r->{$row['name']};
                    }
                    $bind_data = DB::table($row['tablename'])->select($row['tablefield'], $row['tablekey'])->whereIn($row['tablekey'], $ids)->get();
                    $binds = [];
                    foreach ($bind_data as $bd) {
                        $binds[$bd->{$row['tablekey']}] = $bd->{$row['tablefield']};
                    }
                    foreach ($data as &$ref_row) {
                        if (isset($binds[$ref_row->{$row['name']}])) {
                            $ref_row->{$row['name']} = "[" . $ref_row->{$row['name']} . "]" . $binds[$ref_row->{$row['name']}];
                        }
                    }
                }
            }
            return array('total' => $count, 'data' => $data);
        } catch (\Illuminate\Database\QueryException $e) {
            echo $e->getMessage();
            return array('total' => 0, 'data' => []);
        }
    }

    public function getNewCount($modelid, $catid)
    {
        $base = new BaseSetting();
        $info = $base->getDataById($modelid);
        $tableName = $info['name'];
        $yd_min = Date('Y-m-d 0:0:0', strtotime('-1 day'));
        $yd_max = Date('Y-m-d 24:0:0', strtotime('-1 day'));

        $td_min = Date('Y-m-d 0:0:0');
        $td_max = Date('Y-m-d 24:0:0');
        $yd_count = DB::table($tableName)
            ->where('category', $catid)
            ->where('created_at', '>=', $yd_min)
            ->where('created_at', '<', $yd_max)
            ->count();
        $td_count = DB::table($tableName)
            ->where('category', $catid)
            ->where('created_at', '>=', $td_min)
            ->where('created_at', '<', $td_max)
            ->count();
        return array('id' => $catid, 'yestoday' => $yd_count, 'today' => $td_count);
    }

    public function getContent($define, $id)
    {
        $row = DB::table($define['info']['name'])->where('id', $id)->first();
        if ($row) {
            return json_decode(json_encode($row), true);
        }
        return $row;
    }

    public function editContent($define, $id, $data, $catid)
    {
        $id = DB::table($define['info']['name'])->where('category', $catid)->where('id', $id)->update($data);
        return $id;
    }

    public function editContentBatch($define, $ids, $data, $catid)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $id = DB::table($define['info']['name'])->where('category', $catid)->whereIn('id', $ids)->update($data);
        return $id;
    }
    public function editContentRowBatch($define, $data, $catid)
    {
        foreach ($data as $id => $row) {
            $row['updated_at'] = date('Y-m-d H:i:s');
            DB::table($define['info']['name'])->where('category', $catid)->where('id', $id)->update($row);
        }
        return true;
    }
    public function addContent($define, $data)
    {
        $id = DB::table($define['info']['name'])->insertGetId($data);
        return $id > 0;
    }

    public function deleteContent($define, $id, $catid)
    {
        return DB::table($define['info']['name'])->where('id', $id)->where('category', $catid)->delete();
    }

    public function deleteContentBatch($define, $ids, $catid)
    {
        return DB::table($define['info']['name'])->where('category', $catid)->whereIn('id', $ids)->delete();
    }

    public function getStatData($condition, $items, $table, $index, $group_date, $page = 1, $item = '', $month = '')
    {
        if ($page < 1) {
            $page = 1;
        }

        $part1 = [];
        $pagesize = 30;
        $cols = explode(";", $items);
        $counter = 0;
        $header = [];
        foreach ($cols as $col) {
            $c = explode("as", $col);
            $col_name = "item_" . $counter++;
            if (count($c) >= 2) {
                $header[$col_name] = trim($c[1]);
            } else {
                $header[$col_name] = $col_name;
            }
            $part1[] = trim($c[0]) . " as $col_name";
        }
        $addon_where = "1";
        $index_data = [];
        $months = [];
        $days = [];
        $others = [];
        if ($index) {
            $ar = explode("as", $index);
            $sql = "select $ar[0] as `key`,$ar[1] as `name` from $table group by $ar[0] limit " . ($page - 1) * $pagesize . "," . $pagesize;
            $index_data = DB::select($sql);
            if ($item) {
                $addon_where = "$ar[0]=" . $item;
            }
        }
        if ($group_date) {
            $tabs = explode(" ", $table);
            $tb = $tabs[0];
            $month = $month ? $month : date('Ym');
            $extra = "$addon_where and $tb.stat_month=$month";
            $sql = sprintf("select $tb.stat_day,%s from %s where $extra and (%s) group by $tb.stat_day order by $tb.stat_day", implode(",", $part1), $table, $condition);
            $extra = $addon_where;
            $days = DB::select($sql);
            $sql = sprintf("select $tb.stat_month,%s from %s where $extra and (%s) group by $tb.stat_month order by $tb.stat_month", implode(",", $part1), $table, $condition);
            $months = DB::select($sql);
        } else {
            $sql = sprintf("select %s from %s where %s", implode(",", $part1), $table, $condition);
            $others = DB::select($sql);
        }

        return array('header' => $header, 'others' => $others, 'days' => $days, 'months' => $months, 'month' => $month, 'index_data' => $index_data);
    }
}
