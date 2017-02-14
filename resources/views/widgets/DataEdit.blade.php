@require_once('<link rel="stylesheet" href="/adminlte/plugins/datepicker/datepicker3.css">')
@require_once('<link rel="stylesheet" href="/adminlte/plugins/daterangepicker/daterangepicker.css">')
@require_once('<link rel="stylesheet" href="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">')
@require_once('<script src="/adminlte/plugins/ckeditor/ckeditor.js"></script>')
@require_once('<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>')
@require_once('<script src="/adminlte/plugins/daterangepicker/daterangepicker.js"></script>')
@require_once('<script src="/adminlte/plugins/datepicker/bootstrap-datepicker.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
<div class="modal fade" tabindex="-1" role="dialog" id="model_new">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogTitle"></h4>
      </div>
      <div class="modal-body">
          <form role="form" method="post" id="editForm">
              <input type="hidden" name="action" />
              <input type="hidden" name="_token" />
              <input type="hidden" name="id" />
              <div class="box-body">
				  @foreach($inputs as $k=>$input)
	              <div class="form-group">
					@if($input['type']=="text")
					  	<label for="exampleInputEmail1">{{$input['label']}}</label>
  	                	<input type="text" name="{{$input['name']}}" class="form-control" placeholder="{{$input['place_holder']}}" value="{{$input['default']}}">
					@elseif($input['type']=="checkbox")
						<label>
	              			<input type="checkbox" name="{{$input['name']}}">{{$input['label']}}
	                	</label>
					@elseif($input['type']=="html")
                        <label for="exampleInputEmail1">{{$input['label']}}</label>
                        <textarea id="editor_html" name="{{$input['name']}}" class="form-control">
                            {{$input['place_holder']}}
                        </textarea>
					@elseif($input['type']=="datatime")
					@elseif($input['type']=="image")
					@elseif($input['type']=="date")
					@elseif($input['type']=="const")
					@elseif($input['type']=="select")
					@elseif($input['type']=="multiselect")
					@endif
	              </div>
				  @endforeach
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
