@extends('layouts.main')
@section('title', '权限管理')
@section('content')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<form method="post" id="pri_form">
<div class="box">
    <!-- category start -->
    <div class="box-header">
      <h3 class="box-title">栏目权限</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr style="background-color:#e4e4e4">
          <th style="width: 10px">#</th>
          <th style="width: 30%">栏目名</th>
          <th>备注</th>
          <th style="width: 300px">权限</th>
        </tr>
        @foreach($pricats as $k=>$row)
        <tr>
          <td>{{$k+1}}.</td>
          <td><span style="width:{{$row['deep']*15}}px;display:inline-block"></span>{{$row['name']}}</td>
          <td>{{$row['note']}}</td>
          <td>
            <div class="checkbox">
                <label><input type="checkbox" name="view_{{$row['id']}}" value="1"/>显示</label>
                <input type="hidden" name="add_{{$row['id']}}" value="0"/>
                <input type="hidden" name="edit_{{$row['id']}}" value="0"/>
                <input type="hidden" name="del_{{$row['id']}}" value="0"/>
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
      <h3 class="box-title">栏目权限</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr  style="background-color:#e4e4e4">
          <th style="width: 10px">#</th>
          <th style="width: 30%">菜单名</th>
          <th>备注</th>
          <th style="width: 300px">权限</th>
        </tr>
        @foreach($menus as $k=>$row)
        <tr>
          <td>{{$k+1}}.</td>
          <td><span style="width:{{$row['deep']*15}}px;display:inline-block"></span>{{$row['name']}}</td>
          <td>{{$row['note']}}</td>
          <td>
            <div class="checkbox"  style="margin-top:0px;margin-bottom:0px">
                <label><input type="checkbox" name="view_{{$row['id']}}" value="1"/>显示</label>
                <input type="hidden" name="add_{{$row['id']}}" value="0"/>
                <input type="hidden" name="edit_{{$row['id']}}" value="0"/>
                <input type="hidden" name="del_{{$row['id']}}" value="0"/>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- menu end -->
    <!-- model start -->
    <div class="box-header">
      <h3 class="box-title">数据表(模型)权限</h3>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody><tr  style="background-color:#e4e4e4">
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
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="view_{{$row['id']}}" type="checkbox" value=1 />可列表</label></span>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="add_{{$row['id']}}" type="checkbox" value=1 />可增加</label></span>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="edit_{{$row['id']}}"type="checkbox" value=1 />可修改</label></span>
            <span class="checkbox" style="float:left;margin-top:0px"><label><input name="del_{{$row['id']}}" type="checkbox" value=1 />可删除</label></span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    <!-- model end -->
</div>
<button type="submit" class="btn btn-primary">提交修改</button>
<input type="hidden" name="id" value="{{$roleid}}" />
<input type="hidden" name="_token" value="{{csrf_token()}}" />
</form>

<script type="text/javascript">
$(function(){
    $.get("{{$rolepri_url}}?id={{$roleid}}",function(rep){
        dt=rep;
        $("#pri_form").autofill(dt);
    },'json');

    $('#pri_form').ajaxForm({
        url:'{{$modifypri_url}}',
        dataType:'json',
        success:function(rep){
             alert(rep.msg);
        }
    });

});



</script>
@endsection
