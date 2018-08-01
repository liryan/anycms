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
    					  <label for="exampleInputEmail1">统计项</label>
                          <span style="color:red">格式示例[ count(a.orderid) as 订单数,sum(a.monty) as 日成交额 ]</span>
                          <textarea class="form-control" id="statitem" name="item" placeholder="例如:count(userid),sum(money)"></textarea>
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">数据源(表)</label>
                          <span style="color:red">格式示例[ order t left join goods b on(t.goods_id=b.id) ]</span>
    					  <input type="text" name="tablename" class="form-control" placeholder="请输入表名" value="">
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">条件(where)</label>
                          <span style="color:red">格式示例[ t.payed=1 ](注，group by 永远取第一个表的字段)</span>
                          <textarea id="condition" name="condition" class="form-control" placeholder="例如:category=2 and type=3"></textarea>
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">统计目录(参数字段 as 显示字段) </label>
    					  <input type="text" name="index" class="form-control" placeholder="table.pro_id as table.name" value="">
    				  </div>
    				  <div class="form-group">
                          <label>分组按日期（月，日）<input id="gsecond" checked="true" type="checkbox" value="1" name="group_date"></label>
    				  </div>
    				  <div class="form-group">
    					  <label for="exampleInputEmail1">备注 </label>
    					  <input type="text" name="note" class="form-control" placeholder="请输入字符" value="">
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
