@require_once('<link rel="stylesheet" href="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">')
@require_once('<link href="/adminlte/plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/datepicker/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<script src="/adminlte/plugins/ckeditor/ckeditor.js"></script>')
@require_once('<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/plugins/sortable.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/fileinput.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/locales/zh.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/themes/explorer/theme.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/datepicker/locales/bootstrap-datepicker.zh-CN.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')

@import_const(app\Models\DataTable)
<script type="text/javascript">
    var data={
        'language':'zh',
        'theme': 'explorer',
        'uploadUrl': '/admin/content/uploadfile',
        'showCancel':false,
        'showClose':false,
        'showCaption':false,
        overwriteInitial: false,
        initialPreviewAsData: true,
        initialPreview: [],
        initialPreviewConfig: []
    };

    var images=[];

    function init_image(id){
        images.push(id);
        $("#"+id).fileinput(data);
        $("#"+id).css("background","#FFF");
    }

    beforeFillForm=function(data){
        for(img in images){
            var d1=data;
            for(obj in data.keys){
                if(img.indexOf(obj)>0){
                    for(i=0;i<data[obj].length;i++){
                        d1.initialPreview.push(data[obj][i].url);
                        d1.initialPreviewConfig.push({caption:data[obj][i].name , size: data[obj][i].size, width: data[obj][i].width, url: '/admin/content/deletefile', key: data[obj][i].id})
                    }
                    $("#"+img).fileinput(d1);
                }
            }
        }
    }
</script>
<div class="modal fade" tabindex="-1" role="dialog" id="model_new">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogTitle"></h4>
      </div>
      <div class="modal-body">
          <form role="form" method="post" id="edit_form" action="{{$edit_url}}"  enctype="multipart/form-data">
              <input type="hidden" name="action" />
              <input type="hidden" name="_token" />
              <input type="hidden" name="id" />
			  @foreach($inputs as $k=>$input)
    	      <div class="form-group">
					@if($input['type']==DataTable::DEF_INTEGER)
					  	<label for="exampleInputEmail1">{{$input['note']}}</label>
  	                	<input type="text" name="{{$input['name']}}" class="form-control" placeholder="{{$input['comment']}}" value="{{$input['default']}}">
					@elseif($input['type']==DataTable::DEF_CHAR)
						<label  for="exampleInputEmail1">{{$input['note']}}</label>
  	                	<input type="text" name="{{$input['name']}}" class="form-control" placeholder="{{$input['comment']}}" value="{{$input['default']}}">
					@elseif($input['type']==DataTable::DEF_TEXT)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <textarea id="editor_html" name="{{$input['name']}}" class="form-control">
                            {{$input['place_holder']}}
                        </textarea>
                    @elseif($input['type']==DataTable::DEF_DATE)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <input type="text" name="{{$input['name']}}" class="form-control datetimepicker_{{$input['size']}}"  value="" data-date-format=@if($input['size']==1) "yyyy-mm-dd hh:ii" @else "yyyy-mm-dd" @endif>
                    @elseif($input['type']==DataTable::DEF_LIST)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <select name="{{$input['name']}}" class="form-control">
                        @foreach($input['const_list'] as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                        </select>
                    @elseif($input['type']==DataTable::DEF_MULTI_LIST)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        @foreach($input['const_list'] as $item)
                        <label><input type="checkbox" name="{{$input['name']}}[]" value="{{$item['id']}}">{{$item['name']}}</label>
                        @endforeach
					@elseif($input['type']==DataTable::DEF_FLOAT)
					  	<label for="exampleInputEmail1">{{$input['note']}}</label>
  	                	<input type="text" name="{{$input['name']}}" class="form-control" placeholder="{{$input['comment']}}" value="{{$input['default']}}">
                    @elseif($input['type']==DataTable::DEF_IMAGE)
                        <input id="kv-explorer_{{$input['name']}}" type="file" multiple><script type="text/javascript">init_image("kv-explorer_{{$input['name']}}");</script>
					@endif
              </div>
		    @endforeach
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

$('.datetimepicker_1').datetimepicker({
    language:  'zh-CN',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1
});

$('.datetimepicker_2').datepicker({
    language:  'zh-CN',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1
});

beforeFillForm=function(data){
}
</script>
