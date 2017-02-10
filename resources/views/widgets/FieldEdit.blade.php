@require_once('<script src="/adminlte/dist/js/way.min.js"></script>')
<!-- @require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>') -->
<div class="modal fade" tabindex="-1" role="dialog" id="model_new">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span way-data="formdata.dialogTitle"></span></h4>
      </div>
      <div class="modal-body">
          <form role="form" method="post" id="form_1" way-data="formdata" way-persistent>
              <input type="hidden" name="action" />
              <input type="hidden" name="_token" />
              <div class="box-body">
	              <div class="form-group">
					  <label for="exampleInputEmail1">字段名</label>
					  <input type="text" name="note" class="form-control" placeholder="字段名字(中文)" value="">
				  </div>
				  <div class="form-group">
					  <label for="exampleInputEmail1">表字段名</label>
					  <input type="text" name="note" class="form-control" placeholder="字段名字(字符)" value="">
				  </div>
				  <div class="form-group">
					  <label for="exampleInputEmail1">类型</label>
					  <select class="form-control" id="fieldtype">
						<option value="1">数字</<option>
						<option value="2">文本</<option>
						<option value="3">编辑器</<option>
						<option value="4">日期</<option>
						<option value="5">选择列表</<option>
						<option value="6">多选列表</<option>
						<option value="7">图片</<option>
					  </select>
				  </div>
				  <div class="form-group" id="const_table_panel" style="diaplay:none">
				  </div>
				   <div class="form-group">
					   <input type="checkbox" name="listable">是否出现在数据列表中
	              </div>
				  <div class="form-group">
					  <label for="exampleInputEmail1">关联外表字段</label>
					  <div class="row">
						  <div class="col-md-5"><input type="text" name="tablename" class="form-control" placeholder="数据表名" value="1"></div>
					      <div class="col-md-5"><input type="text" name="tablefield" class="form-control" placeholder="字段名" value="1"></div>
				      </div>
				  </div>
				  <div class="form-group">
 					 <label for="exampleInputEmail1">缺省值</label>
 					 <input type="text" name="default" class="form-control" placeholder="数据表名" value="1">
 				 </div>
              </div>
              <!-- /.box-body -->
          </form>
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
			case 2:
			case 3:
			case 4:
			$("#const_table_panel").css("display","none");
			break;
			case 5:
			case 6:
			requireConstData(0);
			break;
			$("#const_table_panel").css("display","none");
			case 7:

		}
	});
});
treepath=new Array();
function requireConstData(id,type){
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
			html+='<li><a href="javascript:requireConstData('+treepath[i].id+')">'+treepath[i].name+'</a></li>';
			if(treepath[i].id==id){
				treepath=treepath.slice(0,i+1);
				break;
			}
		}
		html+="</ol><div class='radio'>";
		for(i=0;i<req.data.length;i++){
			html+='<label><input type="radio" name="const" value='+req.data[i].id+'>'+'<a href="javascript:requireConstData('+req.data[i].id+')">'+req.data[i].name+'</a>'+"</label>";
		}
		html+="</div>";
		$("#const_table_panel").html(html);
		$("#const_table_panel").css("display","block");
	},"json");
}
</script>
