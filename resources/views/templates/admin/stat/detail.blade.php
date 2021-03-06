@extends('layouts.main')
@section('title', '数据统计')
@section('content')
<style>
.tb tbody tr td{
    padding:3px;
}
</style>
<div class="row">
<div class="col-md-2">
    <div class="box box-info">
      <h3 class="box-title" style="margin-left:12px">统计索引</h3>
        <div class="box-body">
        <ul  id="pages">
        @foreach($index_data as $item)
         <li> <a href="{{$curl}}&page={{$page}}&index={{$item->key}}">{{$item->name}}</a></li>
        @endforeach
        </ul>
        </div>
      </div>
</div>
@if($months)
  <div class="col-md-5">
    <div class="box box-info">
      <h3 class="box-title" style="margin-left:12px">{{$stat_name}}-月</h3>
      <div class="box-body">
      <table class="table tb table-bordered">
        <tbody><tr>
          <th style="width: 10px">#</th>
          <th style="width: 60px">日期</th>
          @foreach($header as $item=>$name)
          <th>{{$name}}(共计:@if(isset($month_total[$item])){{$month_total[$item]}}@else 0 @endif)</th>
          @endforeach
        </tr>
        @foreach($months as $k=>$row)
        <tr>
          <td>{{$k}}.</td>
          <td><a href="{{$curl}}&page={{$page}}&index={{$index}}&month={{$row->stat_month}}">{{$row->stat_month}}</a></td>
          @foreach($header as $item=>$name)
          <td>
            <span style="display:inline-block;width:20%">{{$row->{$item} }}</span>
            <div class="progress progress-xs" style="display:inline-block;width:70%;margin-top:2px">
              <div class="progress-bar progress-bar-danger" style="width: @if(@$max_month[$item]!=0){{$row->{$item}*100/$max_month[$item]}}@else{{0}}@endif%"></div>
            </div>
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody></table>
      </div>
    </div><!-- box-info-->
  </div><!--col-->

  <div class="col-md-5">
    <div class="box box-info">
      <h3 class="box-title" style="margin-left:12px">{{$stat_name}}-日</h3>
      <div class="box-body">
      <table class="table tb table-bordered">
        <tbody><tr>
          <th style="width: 10px">#</th>
          <th style="width: 60px">日期</th>
          @foreach($header as $item=>$name)
          <th>{{$name}}(共计:@if(isset($day_total[$item])){{$day_total[$item]}}@else 0 @endif)</th>
          @endforeach
        </tr>
        @foreach($days as $k=>$row)
        <tr>
          <td>{{$k}}.</td>
          <td>{{$row->stat_day}}</td>
          @foreach($header as $item=>$name)
          <td>
            <span style="display:inline-block;width:20%">{{$row->{$item} }}</span>
            <div class="progress progress-xs" style="display:inline-block;width:70%;margin-top:2px">
              <div class="progress-bar progress-bar-danger" style="width: @if(@$max_day[$item]!=0){{$row->{$item}*100/$max_day[$item]}}@else{{0}}@endif%"></div>
            </div>
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody></table>
      </div>
    </div><!-- box-info-->
  </div><!--col-->
@else
<div class="col-md-10">
    <div class="box box-info">
      <h3 class="box-title" style="margin-left:12px">统计结果</h3>
        <div class="box-body">
        <table class="table tb table-bordered">
        <tbody><tr>
          <th style="width: 10px">#</th>
          @foreach($header as $item=>$name)
          <th>{{$name}}</th>
          @endforeach
        </tr>
        @foreach($others as $k=>$row)
        <tr>
          <td>{{$k}}.</td>
          @foreach($header as $item=>$name)
          <td>
            <span style="display:inline-block;width:20%">{{$row->{$item} }}</span>
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody></table>
        </div>
      </div>
</div>
@endif
</div>
@endsection
