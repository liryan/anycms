<style>
    table tbody tr td{
        overflow:hidden;
        word-break:keep-all;
        max-width:300px;
        white-space:nowrap;
    }
    .form-horizontal .form-group{
      margin-left:15px;
      margin-right:15px;
    }
    .form-control{
      margin-top:4px;
    }

</style>
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
              <th>选择/排序</th>
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
  var tableIDS=[];
  var currentEditID=0;
  var currentPage = 0;
  $(function () {
    table=$("#datagrid").DataTable({
        "oLanguage" : {
            "sLengthMenu": "每页显示 _MENU_ 条记录",
            "sZeroRecords": "抱歉， 没有找到",
            "sInfo": "<button class='btn btn-info' style='margin:5px' onclick='submitOrder()'>修改排序</button>跳转到 <input type='text' size='4' value='"+currentPage+"' class='form-control' id='pageNO'> <button class='btn btn-sm' style='margin-top:3px' onclick='gopage()'>GO</button> 从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
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
            tableIDS.push(data.id);
            $(row.cells[0]).html('<input type="checkbox" class="control-label"value="'+data.id+'"> <input class="form-control"  type="text" style="margin-left:5px;width:50px" name="order_'+data.id+'" value="'+data.order+'">');
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
            for(var i=0;i<row.cells.length;i++){
              if($(row.cells[i]).html().toLowerCase().indexOf(".jpg")!=-1||$(row.cells[i]).html().toLowerCase().indexOf(".png")!=-1){
                $(row.cells[i]).html("<img src='"+$(row.cells[i]).html()+"' style='width:100px;height:auto'>");
              }
            }
            @if(isset($model_url))
                var url="{{$model_url}}";
                Object.keys(data).forEach(function(key){
                    url=url.replace("{"+key+"}",data[key]);
                });
                $(row.cells[1]).html("<a title='"+url+"' href='"+url+"' target='_blank'>"+$(row.cells[1]).html()+"</a>");
            @endif
        },
    });

    table.on('page.dt',function(){
      var info= table.page.info();
      currentPage = (info.page);
      console.log(currentPage);
    });

    table.on('draw',function(){
      $("#pageNO").val(currentPage+1);
    });

    table.on('xhr.dt',function(e,settings,json,xhr){
      tableIDS=[];
      if(json.code != undefined && json.code == 401) {
        alert('需要重新登录才可以操作');
        window.location.href="/admin/login";
        return false;
      }
      console.log(json);
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
                    if(rep.code==1){
                      if(confirm(rep.msg+",退出编辑吗?")){
                        $("#model_new").modal('hide');
                        table.ajax.reload(null,false);
                      }
                    }
                    else{
                      alert(rep.msg);
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

function submitOrder()
{
  if(confirm("确定要修改排序吗?")){
    var dom = $("#datagrid :input[type='text']");
    var data={"_token":"{{csrf_token()}}"};
    for(var i=0;i<dom.length;i++){
      name = $(dom[i]).attr('name');
      value = $(dom[i]).val();
      data[name]=value
    }
    ajax_post("/admin/category/modifyorder",data,function(msg){
        alert('已成功修改');
        //table.ajax.reload(null,false);
    },"json");
  }
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
        ajax_post("/admin/content/batchedit",data,function(msg){
            alert('已成功修改');
            table.ajax.reload(null,false);
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
        ajax_post("/admin/content/rowedit",data,function(msg){
            alert('已成功修改');
            table.ajax.reload(null,false);
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
            ajax_post("/admin/content/batchdel",data,function(msg){
                if(msg.code==1){
                    alert('已成功删除');
                    table.ajax.reload(null,false);
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
            ajax_get("/admin/const/?id="+data+"&draw=1",function(msg){
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

    ajax_get(formatUrl("{{$view_url}}","id=0"),function(rep){
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
        ajax_get("{{$preview_url}}&id="+id,function(html){
            $("#model_view").html(html);
            $("#model_view").modal({backdrop: 'static', keyboard: false});
        },'text');
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
        ajax_get(formatUrl("{{$delete_url}}","id="+id),function(rep){
            if(rep.code==0)
                alert(rep.msg);
            else {
                alert("删除成功");
                table.ajax.reload(null,false);
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
    currentEditID=id;
    ajax_get(formatUrl("{{$view_url}}","id="+id),function(rep){
        dt=rep;
        $("#dialogTitle").html("修改")
        beforeFillForm(dt);
        $("#edit_form").autofill(dt);
        endFillForm(dt);
        $("#model_new").modal({backdrop: 'static', keyboard: false});
    },'json');
    $("#model_new").modal();
}

function editBackData()
{
  for(var i=0;i<tableIDS.length;i++){
    if(tableIDS[i]==currentEditID){
      if(i>0){
        editData(tableIDS[i-1]);
      }
      else{
        alert("已超出本页可编辑的范围，请返回列表页点击向下页切换");
      }
      return;
    }
  }
}

function editNextData()
{
  for(var i=0;i<tableIDS.length;i++){
    if(tableIDS[i]==currentEditID){
      if((i+1)<tableIDS.length){
        editData(tableIDS[i+1]);
      }
      else{
        alert("已超出本页可编辑的范围，请返回列表页点击向下页切换");
      }
      return;
    }
  }
}

function gopage(){
  var page = 1*$('#pageNO').val();
  if(page < 1)
    page = 1;
  table.page(page-1).draw(false);
}

//其他编辑url
function modifyField(id)
{
    if("{{$field_url}}"!=""){
        window.location.href=formatUrl("{{$field_url}}","id="+id);
    }
}
</script>
