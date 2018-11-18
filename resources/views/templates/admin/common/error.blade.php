@extends('layouts.main')
@section('title', '出错了')
@section('content')
      <div class="error-page" style="margin-top:200px">
        <h2 class="headline text-red">500</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> Oops! 访问页面出错了.</h3>

          <p>
            <span style="color:red">{{$msg}}</span>
            <p>请联系管理员或者技术人员确定问题!</p>
            <a href="/admin/index">回到首页</a>
          </p>
        </div>
      </div>
      <!-- /.error-page -->
@endsection
