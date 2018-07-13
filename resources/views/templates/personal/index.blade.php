@extends('layouts.main')
@section('title', '模型管理')
@section('content')
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
   <div class="info-box">
       <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
       <div class="info-box-content">
           <span class="info-box-text">快捷导航</span>
            @foreach($cats as $cat)
           <button type="button" class="btn btn-block btn-info" style="display:inline-block;height:50px;width:120px;margin-top:6px" onclick="openW('{{$cat['url']}}')">{{$cat['name']}}</button>
            @endforeach

            @foreach($menus as $menu)
           <button type="button" class="btn btn-block btn-info" style="display:inline-block;height:50px;width:120px;margin-top:6px" onclick="openW('{{$menu['url']}}')">{{$menu['name']}}</button>
            @endforeach
       </div>
       <!-- /.info-box-content -->
   </div>
   <!-- /.info-box -->
    @foreach($news as $new)
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>今日数据:{{$new['today']}}</h3>
          <h4>昨日数据: <span style="color:yellow;font-size:22px">{{$new['yestoday']}}</span></h4>
        </div>
        <a href="/admin/content?catid={{$new['id']}}" class="small-box-footer">查看{{$new['name']}}的详细数据<i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    @endforeach
</div>
<script type="text/javascript">
function openW(url)
{
    if(url.indexOf('http')!=-1){
        window.open(url);
    }
    else{
        window.location.href=url;
    }
}
</script>
@endsection
