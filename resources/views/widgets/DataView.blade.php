<div class="modal-dialog" role="document" style="width:800px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogTitle">数据预览</h4>
      </div>
      <div class="modal-body">
        @foreach($fields as $field)
        <div><h5 style="margin:5px;width:150px;display:inline-block">{{$field['note']}}</h5><span style="display:inline-block;margin:5px">{!!$field['value']!!}</span></div>
        @endforeach
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"> 关闭 </button>
      </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
