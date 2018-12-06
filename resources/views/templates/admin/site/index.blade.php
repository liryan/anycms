@extends('layouts.main')
@section('title', '网站设置')
@section('content')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">网站设置</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="profile_form" method="post" enctype="multipart/form-data">
              <style>.form-control{ display:inline-block;width:auto;min-width:100px} label{margin-left:20px;width:150px;display:inline-block}</style>
              <div class="box-body">
                <div class="form-group">
                  <label for="">网站名</label>
                  <input class="form-control" name="name" placeholder="输入名字" type="name">
                  <label for="">引导语</label>
                  <input class="form-control" name="name" placeholder="输入名字" type="name">
                </div>
                <div class="form-group">
                  <label for="">ICP备案号</label>
                  <input class="form-control" name="icp" placeholder="输入ICP备案号">
                  <label for="">公安备案号</label>
                  <input class="form-control" name="gaicp" placeholder="输入公安备">
                </div>
                <div class="form-group">
                  <label for="">联系我们-电话</label>
                  <input class="form-control" name="contact-tel" placeholder="输入公安备">
                  <label for="">联系我们-邮件</label>
                  <input class="form-control" name="contact-email" placeholder="输入公安备">
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">Logo</label>
                  <input id="" type="file" name="avatar">
                  <img style="margin-top:10px;width:200px;height:auto;background:#000" src="" id="avatar">
                </div>
                <div class="form-group">
                  <label for="">联系我们-公众号二维码</label>
                  <input id="" type="file" name="二维码">
                  <img style="margin-top:10px;width:200px;height:auto;background:#000" src="" id="avatar">
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" class="btn">增加新项-文本</button>
                <button type="button" class="btn">增加新项-图片</button>
                <button type="submit" class="btn btn-primary">提交修改</button>
                <input type="hidden" name="_token" value="{{csrf_token()}}" />
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
      </div>
<script type="text/javascript">
$(function(){
    $.get("{{$view_url}}",function(rep){
        dt=rep;
        if(dt.code==1){
            $("#avatar").attr("src",dt.data.avatar);
            $("#profile_form").autofill(dt.data);
        }
    },'json');

    $('#profile_form').ajaxForm({
        url:'{{$modify_url}}',
        dataType:'json',
        success:function(rep){
             alert(rep.msg);
        }
    });
});
</script>
@endsection
