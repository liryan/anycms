@extends('layouts.main')
@section('title', '出错了')
@section('content')
<div class="row" style="margin-top:100px">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <div class="box box-default">
        <div class="box-header with-border">
          <i class="fa fa-bullhorn"></i>
          <h3 class="box-title"><strong>错误</strong></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="margin:50px">
            <p style="color:red">{{$msg}}</p>
            <p>&nbsp;</p>
            <p>请联系管理员或者技术人员确定问题!</p>
            <p><button class="btn btn-block btn-success" onclick="javascript:history.go(-1);">返回</button></p>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <div class="col-md-3"></div>
    <!-- /.col -->
</div>
@endsection
