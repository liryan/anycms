@extends('layouts.main')
@section('title', '模型管理')
@section('content')
      <div class="error-page" style="margin-top:150px">
        <h2 class="headline text-yellow"> 404</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>

          <p>
            <h4>您正在访问的URL不存在</h4><br/>
            <h5>请确定在routes/web.php中添加<br/>
            URL:[{{$url}}]的路由,并在对应的Controller中增加了对应的处理方法</h5><br/>
             点击 <a href="../../index.html">返回首页</a> 
          </p>
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
@endsection
