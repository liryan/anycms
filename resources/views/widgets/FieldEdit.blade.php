@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<div class="modal fade" tabindex="-1" role="dialog" id="model_new">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogTitle"></h4>
      </div>
      <div class="modal-body">
              <div class="box-body">
                  <form role="form" class="form-horizontal" method="post" id="edit_form">
                      <input type="hidden" id="fortype" />
                      <input type="hidden" name="action" />
                      <input type="hidden" name="_token" />
                      <input type="hidden" name="modelid" value="{{$modelid}}"/>
                      <input type="hidden" name="id" />
    	              <div class="form-group">
    					  <label for="exampleInputEmail1">字段名</label>
    					  <input type="text" name="note" class="form-control" placeholder="字段名字(中文)" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">表字段名</label>
    					  <input type="text" name="name" class="form-control" placeholder="字段名字(字符)" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">类型</label>
    					  <select class="form-control" id="fieldtype" name="type">
                              @foreach($types as $type)
    						<option value="{{$type['value']}}">{{$type['name']}}</<option>
                              @endforeach
    					  </select>
    				  </div>
    				  <div class="form-group" id="const_table_panel" style="diaplay:none"></div>
    				  <div class="form-group">
    					   <label><input type="checkbox" class="control-label" name="listable" value="1">可列表</label>
                           <label><input type="checkbox" class="control-label" name="editable" value="1">可编辑</label>
    	              </div>
    				  <div class="form-group">
        				<label>关联外表字段</label><br/>
                        <label class="col-sm-1 control-label">表</label>
        				<div class="col-sm-5"><input type="text" name="tablename" class="form-control" placeholder="数据表名" value=""></div>
                        <label class="col-sm-2 control-label">字段</label>
        				<div class="col-sm-4"><input type="text" name="tablefield" class="form-control" placeholder="字段名" value=""></div>
    				  </div>
                  </form>
                  <div class="form-group" id="string" style="display:none">
                        <label>设置</label><br/>
                        <label class="col-sm-2 control-label">长度</label>
                        <div class="col-sm-4"><input type="text" name="size" class="form-control" placeholder="长度" value="255"></div>
                        <label class="col-sm-2 control-label">缺省值</label>
                        <div class="col-sm-4"><input type="text" name="default" class="form-control" placeholder="缺省值" value=""></div>
                  </div>
                  <div class="form-group" id="integer" style="display:none">
                        <label>设置</label><br/>
                        <label class="col-sm-2 control-label">长度</label>
                        <div class="col-sm-4"><input type="text" name="size" class="form-control" placeholder="长度" value="11"></div>
                        <label class="col-sm-2 control-label">缺省值</label>
                        <div class="col-sm-4"><input type="text" name="default" class="form-control" placeholder="缺省值" value=""></div>
                  </div>
                  <div class="form-group" id="number" style="display:none">
                    <label>设置</label><br/>
                    <label class="col-sm-2 control-label">整数位宽</label>
                    <div class="col-sm-2"><input type="text" name="size" class="form-control" placeholder="长度" value="8"></div>
                    <label class="col-sm-2 control-label">小数位宽</label>
                    <div class="col-sm-2"><input type="text" name="size_bit" class="form-control" placeholder="长度" value="2"></div>
                    <label class="col-sm-2 control-label">缺省值</label>
                    <div class="col-sm-2"><input type="text" name="default" class="form-control" placeholder="缺省值" value=""></div>
                  </div>
                  <div class="form-group" id="datetime" style="display:none">
                      <label for="exampleInputEmail1">日期类型</label>
                      <select class="form-control" id="datetime" name="size">
                         <option value="1">长格式(2016-08-12 12:10:02)</option>
                         <option value="2">短格式(2016-08-12)</option>
                     </select>
                  </div>
              </div>
              <!-- /.box-body -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"> 关闭 </button>
        <button type="button" class="btn btn-primary" id="submitBt">提交修改</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function(){
	$("#fieldtype").change(function(){
		type=$("#fieldtype").val()*1;
		switch(type){
			case 1:
            showContext('integer');
			case 2:
            showContext('string');
            break;
			case 3:
            $("#const_table_panel").css("display","none");
            break;
			case 4:
            showContext('datetime');
			break;
			case 5:
			case 6:
			requireConstData(0,0);
			break;
			$("#const_table_panel").css("display","none");
			case 7:
            showContext('string');
            break;
            case 8:
            showContext('number');
            break;
		}
	});
    showContext("integer");
});

treepath=new Array();

function showContext(id){
    $("#const_table_panel").html($("#"+id).html());
    $("#const_table_panel").css("display","block");
}

function requireConstData(id,constid){
	$.get("{{$const_url}}?id="+id+"&start=0&length=100&draw=1",function(req){
		if(req.data.length==1)
			return;
		if(id==0){
			treepath.push({id:0,name:'所有'});
		}
		else{
			treepath.push({id:id,name:req.parentname});
		}
		html="<ol class='breadcrumb'>";
		for(i=0;i<treepath.length;i++){
			html+='<li><a href="javascript:requireConstData('+treepath[i].id+',0)">'+treepath[i].name+'</a></li>';
			if(treepath[i].id==id){
				treepath=treepath.slice(0,i+1);
				break;
			}
		}
		html+="</ol><div class='radio'>";
		for(i=0;i<req.data.length;i++){
            check_html='';
            if(req.data[i].id==constid)
                 check_html='checked="checked"';
			html+='<label><input type="radio" name="const" '+check_html+' value='+req.data[i].id+'>'+'<a href="javascript:requireConstData('+req.data[i].id+',0)">'+req.data[i].name+'</a>'+"</label>";
		}
		html+="</div>";
		$("#const_table_panel").html(html);
		$("#const_table_panel").css("display","block");
	},"json");
}

beforeFillForm=function(data){
    type=data.type;
    if(data.id>0){
        $("#fortype").attr("name","type");
        $("#fortype").attr("value",type);
        $("#fieldtype").attr("disabled",true);
    }
    else{
        $("#fieldtype").attr("disabled",false);
    }
    switch(type){
        case 1:
        showContext('integer');
        case 2:
        showContext('string');
        break;
        case 3:
        $("#const_table_panel").css("display","none");
        break;
        case 4:
        showContext('datetime');
        break;
        case 5:
        case 6:
        treepath.push({id:0,name:'所有'});
        requireConstData(data.const_parentid,data.const);
        break;
        $("#const_table_panel").css("display","none");
        case 7:
        showContext('string');
        break;
        case 8:
        showContext('number');
        break;
    }
}
</script>
