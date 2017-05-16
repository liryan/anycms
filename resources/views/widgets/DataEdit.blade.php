@require_once('<link href="/adminlte/plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/datepicker/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/umeditor1.2.3-utf8-php/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">')
@require_once('<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.formautofill.min.js"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/plugins/sortable.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/fileinput.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/js/locales/zh.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/fileinput/themes/explorer/theme.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/datepicker/locales/bootstrap-datepicker.zh-CN.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" type="text/javascript"></script>')
@require_once('<script src="/adminlte/plugins/jQuery/jquery.form.js"></script>')
@require_once('<script type="text/javascript" src="/adminlte/plugins/umeditor1.2.3-utf8-php/third-party/template.min.js"></script>')
@require_once('<script type="text/javascript" charset="utf-8" src="/adminlte/plugins/umeditor1.2.3-utf8-php/umeditor.config.js"></script>')
@require_once('<script type="text/javascript" charset="utf-8" src="/adminlte/plugins/umeditor1.2.3-utf8-php/umeditor.min.js"></script>')
@require_once('<script type="text/javascript" src="/adminlte/plugins/umeditor1.2.3-utf8-php/lang/zh-cn/zh-cn.js"></script>')

@import_const(app\Models\DataTable)
<script type="text/javascript">
    var Editor=new Array();
    var option_data={
        'language':'zh',
        'theme': 'explorer',
        'uploadUrl': '/admin/content/uploadimage',
        'showCancel':false,
        'showClose':false,
        'showCaption':false,
        'showRemove':false,
        overwriteInitial: false,
        initialPreviewAsData: true,
        initialPreview: [],
        initialPreviewConfig: []
    };

    var images=[];
    var UploadFile=[];

    function changeValue(id,url,isadd)
    {
        fileobj=$("#"+id);
        if(fileobj.attr("single")=="1"){
            fileobj.val(url); 
        }
        else{
            str=fileobj.val();
            if(str){
                obj=JSON.parse(str);
            }
            else{
                obj=[];
            }
            if(isadd){
                obj.push(url);
            }
            else{
                newobj=[];
                for(i=0;i<obj.length;i++){
                    if(obj[i]!=url){
                        newobj.push(obj[i]);
                    }
                }
                obj=newobj;
            }
            fileobj.val(JSON.stringify(obj));
        }
    }

    function uploaded(event,data,preview_id){
        url=data.response.url;
        id=data.response.id.replace("file_","");
        obj=$("#"+id);
        fileobj=$("#"+data.response.id);
        UploadFile.push({id:id,url:url,keyid:preview_id});
        changeValue(id,url,true);
    }

    function deletePic(event, key, jqXHR, data)
    {
        alert($(this).attr("name")); 
    }

    function removePic(event, id, index)
    {
        for(i=0;i<UploadFile.length;i++){
            if(UploadFile[i].keyid==id){
                changeValue(UploadFile[i].id,UploadFile[i].url,false);    
            }
        }
    }

    function init_image(id){
        images.push(id);
        $("#"+id).fileinput(option_data).on("fileuploaded",uploaded);
        $("#"+id).on("filedeleted",deletePic);
        $("#"+id).on("fileremoved",removePic);
        $("#"+id).css("background","#FFF");
    }

    beforeFillForm=function(data){
        for(img in images){
            var d1=option_data;
            for(obj in data){
                if(images[img].indexOf(obj)>0){
                    if(typeof data[obj] =="string"){
                        d1.initialPreview.push(data[obj]);
                        d1.initialPreviewConfig.push({caption:'' , size: '', width: '', url: '/admin/content/deletefile', key: ''})
                    }
                    else{
                        for(i=0;i<data[obj].length;i++){
                            d1.initialPreview.push(data[obj][i].url);
                            d1.initialPreviewConfig.push({caption:'', size: '', width: '', url: '/admin/content/deletefile', key: ''})
                        }
                    }

                    $("#"+images[img]).fileinput(d1);
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
                        <textarea id="editor_html" name="{{$input['name']}}" class="form-control"></textarea>
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
  	                	<input type="text" name="{{$input['name']}}" name="file_{{$input['name']}}" class="form-control" placeholder="{{$input['comment']}}" value="{{$input['default']}}">
                        <input type="hidden" id="{{$input['name']}}" value="" single="0" name="{{$input['name']}}">
                    @elseif($input['type']==DataTable::DEF_IMAGES)
                        <input id="kv-explorer_{{$input['name']}}" name="file_{{$input['name']}}" type="file" multiple="true"><script type="text/javascript">init_image("kv-explorer_{{$input['name']}}");</script>
                        <input type="hidden" id="{{$input['name']}}" value="" name="{{$input['name']}}">
                    @elseif($input['type']==DataTable::DEF_IMAGE)
                        <input id="kv-explorer_{{$input['name']}}" name="file_{{$input['name']}}" type="file" multiple="true"><script type="text/javascript">init_image("kv-explorer_{{$input['name']}}");</script>
                        <input type="hidden" id="{{$input['name']}}" value="" name="{{$input['name']}}">
                    @elseif($input['type']==DataTable::DEF_EDITOR)
					  	<label for="exampleInputEmail1">{{$input['note']}}</label>
                        <script type="text/javascript">Editor.push("{{$input['id']}}");</script>  
                        <script type="text/plain" id="editor_{{$input['id']}}" style="height:240px;">
                            <p>这里我可以写一些输入提示</p>
                        </script>
  	                	<input type="hidden" name="{{$input['name']}}" id="editor_value_{{$input['id']}}">
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
$(function(){
    editor_config={
        imageUrl:"/admin/content/uploadfile" 
        ,imagePath:"" 
        ,imageFieldName:"upload_file"
    };

    for(i=0;i<Editor.length;i++)
    {
        UM.getEditor("editor_"+Editor[i],editor_config);
    }

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
});

beforeSubmit=function()
{
    for(i=0;i<Editor.length;i++)
    {
        $("#editor_value_"+Editor[i]).val(UM.getEditor("editor_"+Editor[i]).getContent());
    }
}

endFillForm=function()
{
    for(i=0;i<Editor.length;i++)
    {
        UM.getEditor("editor_"+Editor[i]).setContent($("#editor_value_"+Editor[i]).val());
    }
}
</script>
