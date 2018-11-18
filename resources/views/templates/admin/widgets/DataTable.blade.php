<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">{{$name}}</h3>
            @if($pri[1]==1)
   			  <div class="pull-right"><button class="btn bg-orange margin" onclick="addData()">[+]新增</button></div>
            @endif
          </div>
          <!-- /.box-header -->
          <div class="row">
          {!!$search_widget!!}
          </div>
          <div class="box-body">
            <style>.table tbody tr td{vertical-align:middle;} .form-control{padding:2px 2px;height:27px}</style>
            <table width="100%" id="datagrid" class="table table-bordered table-hover">
              <thead>
              <tr>
                  <th>选择</th>
				  @foreach($fields as $field)
                	<th>{{$field['note']}}</th>
				  @endforeach
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            @if(isset($catid) && $catid)
            <div style="width:100%">
             <span style="float:left;display:inline-block">
                全选  
                <input type="checkbox" class="control-label" style="margin-top:5px" id="selectAll" "checked"=false onclick="checkAll()">
                把 
                <select id="edit_field" value="0">
                    <option value="0">选择要修改的字段</option>
                    @foreach($fields as $field)
                    @if(@$field['batchable']==1)
                    <option id="{{$field['name']}}" value="{{$field['name']}}|{{$field['type']}}" data="@if($field['type']==5){{$field['const']}}@elseif($field['type']==4){{$field['size']}}@endif">{{$field['note']}}</option>
                    @endif
                    @endforeach
                </select>
                <span id="label_panel">修改成</span>
                <span id="edit_panel">
                </span>
            </span>
            <span style="float:right;display:inline-block">
            <span id="batch_submit">
            </span>
            <button style='margin-bottom:5px;' type="button" class="btn btn-danger" onclick="submitDelete()">删除所有选择</button>
            </span>
            </div>
            @endif
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
<!-- 编辑对话框组件,绑定下面的js代码 -->
<script type="text/javascript">
beforeFillForm=function(data){}
endFillForm=function(data){}
beforeSubmit=function(){}
</script>
{!!$new_dialog!!}
<div class="modal fade" tabindex="-1" role="dialog" id="model_view"></div>
<!-- /编辑对话框组件,绑定下面的js代码 -->
<script src="/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
  var showbatchButton=false;
  var table=null;
  $(function () {
    table=$("#datagrid").DataTable({
        "oLanguage" : {
            "sLengthMenu": "每页显示 _MENU_ 条记录",
            "sZeroRecords": "抱歉， 没有找到",
            "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
            "sInfoEmpty": "没有数据",
            "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
            "sZeroRecords": "没有检索到数据",
             "sSearch": "名称:",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "前一页",
                "sNext": "后一页",
                "sLast": "尾页"
             }
        },

        "processing":true,
        "serverSide":true,
        "paging": true,
        "striped":true,
        "searching":false,
        "ajax":"{{$url}}",
        "columns":[
             {"data":"id"},
			@foreach($fields as $field)
            {"data":"{{$field['name']}}"},
			@endforeach
        ],
        "rowCallback": function( row, data ,index) {//添加单击事件，改变行的样式
            $(row.cells[0]).html('<input type="checkbox" class="control-label"value="'+data.id+'">');
            @if($pri[0]==1)
                $(row.cells[row.cells.length-1]).html('<a onclick="viewData('+data.id+',{{isset($catid)?1:0}})" class="btn btn-success btn-sm" id="viewbt">查看</a> ');
            @endif
            @if($pri[2]==1)
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="editData('+data.id+')" class="btn btn-warning  btn-sm">修改</a> ');
            @endif
            @if($pri[3]==1) //delete
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="deleteData('+data.id+')" class="btn  btn-danger btn-sm">[-]删除</a> ');
            @endif
            @if($pri[4]==1) //
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="modifyField('+data.id+')" class="btn  btn-info btn-sm">[-]修改字段</a> ');
            @endif
            @if(isset($model_url))
                var url="{{$model_url}}";
                Object.keys(data).forEach(function(key){
                    url=url.replace("{"+key+"}",data[key]);
                });
                $(row.cells[1]).html("<a title='"+url+"' href='"+url+"' target='_blank'>"+$(row.cells[1]).html()+"</a>");
            @endif
  
            @foreach($fields as $k=>$field)
                @if(@$field['orderable']==1)
                showbatchButton=true;
                $("#batch_submit").html("<button style='margin-bottom:5px' type='button' class='btn btn-warning' onclick='submitBatchEdit()'>提交本页修改</button>");
                $(row.cells[{{$k+1}}]).html("<input type='edit' class='form-control' title='"+$(row.cells[{{$k+1}}]).html()+"' id='batch_field' value='"+$(row.cells[{{$k+1}}]).html()+"' name='{{$field['name']}}-"+data.id+"' class='' size=10>");
                @endif
            @endforeach
            //data._internal_field='ok';
        },
    });
});
//数据表中的编辑对话框的提交处理


$(function(){
    $('#edit_field').change(function(){
        changeSelectField();
    });

    $('#edit_form').ajaxForm({
        url:'{{$edit_url}}',
        dataType:'json',
        success:function(rep){
                    alert(rep.msg);
                    if(rep.code==1){
                        $("#model_new").modal('hide');
                        table.ajax.reload();
                    }
                }
    });
	$('#submitBt').on('click', function (e) {
        beforeSubmit();
        $("#edit_form").submit();
	});

    $('#search_form').submit(function(){
            keyword=$('#search_form').serialize();
            table.ajax.url("{{$url}}&"+keyword).load();
            return false;
    });
    changeSelectField();
});

function checkAll()
{
    $("#datagrid :checkbox").prop("checked",$("#selectAll").prop("checked"));
}

function submitEdit()
{
    if(confirm('确定要修改所选的条目吗?')){
        chks=$("#datagrid :checkbox");
        ids='0';
        for(i=0;i<chks.length;i++){
            if($(chks[i]).prop("checked")){
                ids+="-"+$(chks[i]).val();
            }
        }
        data={catid:'{{isset($catid)?$catid:0}}',name:$("#edit_field").val(),value:$("#field_value").val(),ids:ids,"_token":"{{csrf_token()}}"};
        $.post("/admin/content/batchedit",data,function(msg){
            alert('已成功修改');
            table.ajax.reload();
        },"json");
    }
}

function submitBatchEdit(){
    if(confirm('确定提交每行修改的数据吗?')){
        editbox=$("#batch_field");
        var data={};
        for(i=0;i<editbox.length;i++){
            data[$(editbox[i]).attr("name")]=$(editbox[i]).val();
        }
        data.catid='{{isset($catid)?$catid:0}}';
        data._token='{{csrf_token()}}';
        $.post("/admin/content/rowedit",data,function(msg){
            alert('已成功修改');
            table.ajax.reload();
        },"json");
    }
}

function submitDelete()
{
    if(confirm('这个操作很危险，你确定要删除所选的所有数据吗?')){
        if(confirm('危险！你正在删除多个数据')){
            chks=$("#datagrid :checkbox");
            ids='0';
            for(i=0;i<chks.length;i++){
                if($(chks[i]).prop("checked")){
                    ids+="-"+$(chks[i]).val();
                }
            }
            data={catid:'{{isset($catid)?$catid:0}}',name:$("#edit_field").val(),value:$("#field_value").val(),ids:ids,'_token':'{{csrf_token()}}'};
            $.post("/admin/content/batchdel",data,function(msg){
                if(msg.code==1){
                    alert('已成功删除');
                    table.ajax.reload();
                }
                else{
                    alert('删除失败');
                }
            },"json");
        }
    }
}

function changeSelectField()
{
    v=$("#edit_field").val();
    $("#edit_panel").html("");
    $("#label_panel").html("");
    if(v === undefined){
        return;
    }
    if(v!=0){
        $("#label_panel").html("修改成");
        option=v.split("|");
        if(option[1]==1 || option[1]== 2 || option[1]==8){　//整数，字符串，浮点
            $("#edit_panel").html("<input type='text' name='field_value'>");
        }
        else if(option[1]==5){  //常量列表
            data=$("#"+option[0]).attr('data');
            $("#edit_panel").html("<select id='field_value'></select>");
            $.get("/admin/const/?id="+data+"&draw=1",function(msg){
                if(msg.recordsTotal>0){
                    for(i=0;i<msg.data.length;i++){
                        node = msg.data[i];
                        $("#field_value").append("<option value='"+node.value+"'>"+node.name+"</opton>");
                    }
                }
            },"json");
        }
        else if(option[1]==4){  //日期
            data=$("#"+option[0]).attr('data');
            if(option[2]==1){
                $("#edit_panel").html('<input type="datetime" id="field_value" name="field_value" class="">');
            }
            else{
                $("#edit_panel").html('<input type="date" id="field_value" name="field_value" class="">');
            }
        }
        $("#edit_panel").html($("#edit_panel").html()+"<button style='margin-bottom:5px' type='button' class='btn btn-warning' onclick='submitEdit()'>确认提交</button>");
    }
}
//显示新数据对话框
function formatUrl(url,paramstring)
{
    if(url.indexOf('?')>0){
        url+="&"+paramstring;
    }
    else {
        url+="?"+paramstring;
    }
    return url;
}

function addData(){
    var origin_id=@if(isset($id)){{$id}}@else 0 @endif;
    $("#edit_form")[0].reset();

    $.get(formatUrl("{{$view_url}}","id=0"),function(rep){
        dt=rep;
        dt.id=origin_id;
        beforeFillForm(dt);
        $("#dialogTitle").html("添加")
        $("#edit_form").autofill(dt);
        endFillForm(dt);
        $("#model_new").modal({backdrop: 'static', keyboard: false});
    },'json');
}

//显示查看对话框
function viewData(id,priview){
    if(priview){
        @if(isset($preview_url))
        $.get("{{$preview_url}}&id="+id,function(html){
            $("#model_view").html(html);
            $("#model_view").modal({backdrop: 'static', keyboard: false});
        });
        @else
            alert('没提供查看功能');
        @endif
    }
    else
        window.location.href=formatUrl("{{$open_url}}","id="+id);
}

//删除数据
function deleteData(id){
    if(confirm("确定要删除吗")){
        $.get(formatUrl("{{$delete_url}}","id="+id),function(rep){
            if(rep.code==0)
                alert(rep.msg);
            else {
                alert("删除成功");
                table.ajax.reload();
            }
        },'json');
    }
}

//编辑数据
function editData(id){
    if(id<0){
        alert("无效的参数");
        return;
    }
    $.get(formatUrl("{{$view_url}}","id="+id),function(rep){
        dt=rep;
        $("#dialogTitle").html("修改")
        beforeFillForm(dt);
        $("#edit_form").autofill(dt);
        endFillForm(dt);
        $("#model_new").modal({backdrop: 'static', keyboard: false});
    },'json');
    $("#model_new").modal();
}

//其他编辑url
function modifyField(id)
{
    if("{{$field_url}}"!=""){
        window.location.href=formatUrl("{{$field_url}}","id="+id);
    }
}
</script>
