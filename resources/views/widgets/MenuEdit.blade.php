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
    					  <label for="exampleInputEmail1">菜单名字</label>
    					  <input type="text" name="name" class="form-control" placeholder="请输入中文" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">URL(会自动增加前缀<span style="color:red">/admin/ext</span>)</label>
    					  <input type="text" name="note" class="form-control" placeholder="URL" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">菜单样式<查看实例></label>
    					  <input type="text" name="setting" class="form-control" placeholder="样式" value="fa fa-list">
    				  </div>
    				  <div class="form-group">
                        <h5>添加完菜单，需要在app/Http/Controllers/Admin/ExtController中添加对应的方法<h5>
                        <h6>例如: 添加菜单url为Order/Stat，在ExtController中添加getOrderStat(Request $req)<h6>
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
