@extends('layouts.main')
@section('title', '个人设置')
@section('content')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<form method="post" id="setting_form">
<div class="box">
    <!-- category start -->
    <div class="box-header">
      <h3 class="box-title">个人设置</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr style="background-color:#e4e4e4">
          <th style="width: 10px">#</th>
          <th style="width: 30%">栏目名</th>
          <th style="width: 150px">订阅</th>
          <th style="width: 150px">添加到工作台</th>
        </tr>
        @foreach($cats as $k=>$row)
        <tr>
          <td style="vertical-align:middle">{{$k+1}}.</td>
          <td style="vertical-align:middle">
            <span style="width:{{$row['deep']*15}}px;display:inline-block"></span>[{{$row['note']}}]{{$row['name']}}
          </td>
          <td>
            <div>
                <label><input type="checkbox" name="catsub_{{$row['id']}}" value="1"/>订阅</label>
            </div>
          </td>
          <td>
            <div>
                <label><input type="checkbox" name="catview_{{$row['id']}}" value="1"/>添加</label>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- category end -->
    <!-- menu start -->
    <div class="box-header">
      <h3 class="box-title">扩展菜单</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr  style="background-color:#e4e4e4">
          <th style="width: 10px">#</th>
          <th style="width: 30%">栏目名</th>
          <th style="width: 150px">添加到工作台</th>
        </tr>
        @foreach($menus as $k=>$row)
        <tr>
          <td style="vertical-align:middle">{{$k+1}}.</td>
          <td style="vertical-align:middle"><span style="width:{{$row['deep']*15}}px;display:inline-block"></span>[{{$row['note']}}]{{$row['name']}}</td>
          <td>
            <div class="checkbox"  style="margin-top:0px;margin-bottom:0px">
                <label><input type="checkbox" name="menuview_{{$row['id']}}" value="1"/>添加</label>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- menu end -->
</div>
<button type="submit" class="btn btn-primary">提交修改</button>
<input type="hidden" name="_token" value="{{csrf_token()}}" />
</form>

<script type="text/javascript">
$(function(){
    $.get("{{$get_url}}",function(rep){
        dt=rep;
        $("#setting_form").autofill(dt.data);
    },'json');

    $('#setting_form').ajaxForm({
        url:'{{$save_url}}',
        dataType:'json',
        success:function(rep){
             alert(rep.msg);
        }
    });

});
</script>
@endsection
