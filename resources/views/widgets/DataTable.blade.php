<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">{{$name}}</h3>
          </div>
          <!-- /.box-header -->
		  <div class="box-body">
			  <div class="pull-right"><button class="btn bg-orange margin" onclick="addData()">[+]新增</button></div>
		  </div>
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
              <tfoot>
              <tr>
				@foreach($fields as $field)
  				  <th>{{$field['label']}}</th>
  				@endforeach
              </tr>
              </tfoot>
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
            if(data._internal_field.length>=4 && data._internal_field[1]==1){
                $(row.cells[row.cells.length-1]).html('<a onclick="viewData('+data.id+')" class="btn btn-success btn-sm" id="viewbt">查看</a> ');
            }
            if(data._internal_field.length>=4 && data._internal_field[2]==1){
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="editData('+data.id+')" class="btn btn-warning  btn-sm">修改</a> ');
            }
            if(data._internal_field.length>=4 && data._internal_field[3]==1){ //delete
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="deleteData('+data.id+')" class="btn  btn-danger btn-sm">[-]删除</a> ');
            }
            if(data._internal_field.length>=4 && data._internal_field[4]==1){ //delete
                $(row.cells[row.cells.length-1]).html($(row.cells[row.cells.length-1]).html()+'<a onclick="modifyField('+data.id+')" class="btn  btn-info btn-sm">[-]修改字段</a> ');
            }
            //data._internal_field='ok';
        },
    });
});
//数据表中的编辑对话框的提交处理
$(function(){
	$('#submitBt').on('click', function (e) {
        dt=$('#editForm').serializeArray();
        $.post("{{$edit_url}}" ,dt,function(req){
            alert(req.msg);
        },"json");
	});
    //CKEDITOR.replace('editor_html');
});

//显示新数据对话框
function addData(){
    $.get("{{$view_url}}?id=0",function(rep){
        dt=rep;
        dt.dialogTitle="添加";
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
        dt.dialogTitle="修改";
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
