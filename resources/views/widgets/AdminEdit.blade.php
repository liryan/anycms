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
                      <input type="hidden" name="id" value="{{$id}}"/>
    	              <div class="form-group">
    					  <label for="exampleInputEmail1">名字</label>
    					  <input type="text" name="name" class="form-control" placeholder="请输入昵称" value="">
    				  </div>
    	              <div class="form-group">
    					  <label for="exampleInputEmail1">邮件</label>
    					  <input type="text" name="name" class="form-control" placeholder="请输入账号" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">密码</label>
    					  <input type="text" name="note" class="form-control" placeholder="请输入字符" value="">
    				  </div>
					 <div class="form-group">
						<label for="exampleInputEmail1">绑定角色</label>
                        <input type="checkbox" value="0">管理员
                        @foreach($roles as $row)
                        <input type="checkbox" value="{{$row['id']}}">{{$row['note']}}
                        @endforeach
					 </div>
                  </form>
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
});

beforeFillForm=function(data){
}
</script>