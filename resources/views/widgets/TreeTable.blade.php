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
            <table id="datagrid" class="table table-bordered table-hover">
              <thead>
              <tr>
				  @foreach($fields as $field)
                	<th>{{$field['note']}}</th>
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
endFillForm=function(){}
beforeSubmit=function(){}
</script>
{!!$new_dialog!!}
<!-- /编辑对话框组件,绑定下面的js代码 -->
<script src="/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
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
        "uniqueId": "id",
        "clickToSelect": true,
        "processing":true,
        "serverSide":true,
        "paging": true,
        "searching":false,
        "ajax":"{{$url}}",
        'detailView':true,
        "columns":[
			@foreach($fields as $field)
            {"data":"{{$field['name']}}"},
			@endforeach
        ],
        "rowCallback": function( row, data ,index) {//添加单击事件，改变行的样式
            @if($pri[0]==1)
                $(row.cells[row.cells.length-1]).html('<a onclick="viewData('+data.id+')" class="btn btn-success btn-sm" id="viewbt">查看</a> ');
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
            //data._internal_field='ok';
        },
        'onExpandRow': function (index, row, $subdetail) {
            init_subtable(index,row,$subdetail);
        }
    });
});

function init_subtable(index,row,$detail)
{
    var parentid = row.id;
    var cur_table = $detail.html('<table></table>').find('table');
    $(cur_table).bootstrapTable({
        url: '/api/MenuApi/GetChildrenMenu',
        method: 'get',
        queryParams: { strParentID: parentid },
        ajaxOptions: { strParentID: parentid },
        clickToSelect: true,
        detailView: true,//父子表
        uniqueId: "id",
        pageSize: 10,
        pageList: [10, 25],
        columns: [{
            checkbox: true
        }, {
            field: 'MENU_NAME',
            title: '菜单名称'
        }, {
            field: 'MENU_URL',
            title: '菜单URL'
        }, {
            field: 'PARENT_ID',
            title: '父级菜单'
        }, {
            field: 'MENU_LEVEL',
            title: '菜单级别'
        }, ],
        //无线循环取子表，直到子表里面没有记录
        onExpandRow: function (index, row, $Subdetail) {
            init_subtable(index, row, $Subdetail);
        }
    });
}
//数据表中的编辑对话框的提交处理


$(function(){
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
});

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
    $("#edit_form")[0].reset();

    $.get(formatUrl("{{$view_url}}","id=0"),function(rep){
        dt=rep;
        beforeFillForm(dt);
        $("#dialogTitle").html("添加")
        $("#edit_form").autofill(dt);
        endFillForm();
        $("#model_new").modal({backdrop: 'static', keyboard: false});
    },'json');
}

//显示查看对话框
function viewData(id){
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
        endFillForm();
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
