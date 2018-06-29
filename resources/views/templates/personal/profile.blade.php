@extends('layouts.main')
@section('title', '模型管理')
@section('content')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<div class="row">
        <!-- left column -->
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">个人信息</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="profile_form" method="post" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="">账号</label>
                  <input class="form-control" readonly="true" name="email" placeholder="输入名字" type="email">
                </div>
                <div class="form-group">
                  <label for="">名字</label>
                  <input class="form-control" name="name" placeholder="输入名字">
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">原密码</label>
                  <input class="form-control" name="old_password" placeholder="输入原有密码" type="password">
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">密码</label>
                  <input class="form-control" name="password" placeholder="输入新密码" type="password">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">重复密码</label>
                  <input class="form-control" name="repassword" placeholder="再次输入新密码" type="password">
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">头像</label>
                  <input id="" type="file" name="avatar">
                  <img style="width:200px;height:auto" src="/avatar/6c5d7fe1a44540734ba00f4ec64f783a9de86feb.jpeg" id="avatar">
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">提交修改</button>
                <input type="hidden" name="_token" value="{{csrf_token()}}" />
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <div class="col-md-2"></div>
        <!--/.col (left) -->
      </div>
<script type="text/javascript">
$(function(){
    $.get("{{$profile_url}}",function(rep){
        dt=rep;
        if(dt.code==1){
            $("#avatar").src=dt.data.avatar;
            $("#profile_form").autofill(dt.data);
        }
    },'json');

    $('#profile_form').ajaxForm({
        url:'{{$modifyprofile_url}}',
        dataType:'json',
        success:function(rep){
             alert(rep.msg);
        }
    });
});
</script>
@endsection
