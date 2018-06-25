@extends('layouts.main')
@section('title', '权限管理')
@section('content')
<form>
<div class="box">
    <!-- category start -->
    <div class="box-header">
      <h3 class="box-title">栏目权限</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr>
          <th style="width: 10px">#</th>
          <th style="width: 30%">栏目名</th>
          <th>备注</th>
          <th style="width: 300px">权限</th>
        </tr>
        @foreach($pricats as $k=>$row)
        <tr>
          <td>{{$k+1}}.</td>
          <td>{{$row['name']}}</td>
          <td>{{$row['note']}}</td>
          <td>
            <div class="checkbox"><label><input type="checkbox" name="cat_{{$row['id']}}" value="1"/>显示</label></div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- category end -->
    <!-- model start -->
    <div class="box-header">
      <h3 class="box-title">数据表(模型)权限</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr>
          <th style="width: 10px">#</th>
          <th style="width: 30%">数据表(模型)</th>
          <th>备注</th>
          <th style="width: 300px">权限</th>
        </tr>
        @foreach($primodels as $k=>$row)
        <tr>
          <td>{{$k+1}}.</td>
          <td>{{$row['name']}}</td>
          <td>{{$row['note']}}</td>
          <td>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="add_{{$row['id']}}" type="checkbox" value=1 />可增加</label></span>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="del_{{$row['id']}}" type="checkbox" value=1 />可删除</label></span>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="edit_{{$row['id']}}"type="checkbox" value=1 />可修改</label></span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- model end -->
</div>
</form>
@endsection
