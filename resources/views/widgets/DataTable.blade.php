<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">{{$name}}</h3>
          </div>
          <!-- /.box-header -->
          @if($pri[0]==1)
		  <div class="box-body">
			  <div class="pull-right"><button class="btn bg-orange margin" onclick="addData()">[+]新增</button></div>
		  </div>
          @endif
          <div class="box-body">
            <table id="datagrid" class="table table-bordered table-hover">
              <thead>
              <tr>
				  @foreach($fields as $field)
                	<th>{{$field['label']}}</th>
				  @endforeach
              </tr>
              </thead>
              <tbody>
              </tbody>

            </table>
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
</script>
{!!$new_dialog!!}
<!-- /编辑对话框组件,绑定下面的js代码 -->
<script src="/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
  $(function () {
    $("#datagrid").DataTable({
        "processing":true,
        "serverSide":true,
        "paging": true,
        "searching":false,
        "ajax":"{{$url}}",
        "columns":[
			@foreach($fields as $field)
            {"data":"{{$field['name']}}"},
			@endforeach
        ],
        "rowCallback": function( row, data ,index) {//添加单击事件，改变行的样式
            @if($pri[3]==1)
                $(row.cells[row.cells.length-1]).html('<a onclick="viewData('+data.id+')" class="btn btn-success btn-sm" id="viewbt">查看</a> ');
            @endif
            @if($pri[2]==1)
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="editData('+data.id+')" class="btn btn-warning  btn-sm">修改</a> ');
            @endif
            @if($pri[1]==1) //delete
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="deleteData('+data.id+')" class="btn  btn-danger btn-sm">[-]删除</a> ');
            @endif
            @if($pri[4]==1) //
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="modifyField('+data.id+')" class="btn  btn-info btn-sm">[-]修改字段</a> ');
            @endif
            //data._internal_field='ok';
        },
    });
});
//数据表中的编辑对话框的提交处理


$(function(){
    $('#editForm').ajaxForm({
        url:'{{$edit_url}}',
        dataType:'json',
        success:function(rep){
                    alert(rep.msg);
                }
    });
	$('#submitBt').on('click', function (e) {
        $("#editForm").submit();
	});
    //CKEDITOR.replace('editor_html');
});

//显示新数据对话框
function addData(){
    $("#editForm")[0].reset();
    $.get("{{$view_url}}?id=0",function(rep){
        dt=rep;
        beforeFillForm(dt);
        $("#dialogTitle").html("添加")
        $("#editForm").autofill(dt);
        $("#model_new").modal();
    },'json');
}

//显示查看对话框
function viewData(id){
    if(id<0){
        alert("无效的参数");
        return;
    }
    $.get("{{$view_url}}?id="+id,function(rep){
        dt=rep;
        $("#editForm").autofill(dt);
        $("#model_new").modal();
    },'json');
    $("#model_new").modal();
}

//删除数据
function deleteData(id){
    if(confirm("确定要删除吗")){
        $.get("{{$delete_url}}?id="+id,function(rep){
            if(rep.code==0)
                alert(rep.msg);
            else {
                alert("删除成功");
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
    $.get("{{$view_url}}?id="+id,function(rep){
        dt=rep;
        $("#dialogTitle").html("修改")
        beforeFillForm(dt);
        $("#editForm").autofill(dt);
        $("#model_new").modal();
    },'json');
    $("#model_new").modal();
}

//其他编辑url
function modifyField(id)
{
    if("{{$field_url}}"!=""){
        window.location.href="{{$field_url}}?id="+id;
    }
}
</script>
