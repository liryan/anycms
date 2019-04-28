@require_once('<link href="/adminlte/plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/datepicker/datepicker3.css" media="all" rel="stylesheet" type="text/css"/>')
@require_once('<link href="/adminlte/plugins/umeditor1.2.3-utf8-php/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">')
@require_once('<script src="/adminlte/plugins/jQuery/moment.min.js"></script>')
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
        initialPreviewFileType:'image',
        overwriteInitial: false,
        initialPreviewAsData: false,
        initialPreview: [],
        initialPreviewConfig: []
    };

    var images=[];
    var UploadFile=[];

    function changeValue()
    {
        ids=[];
        for(i=0;i<UploadFile.length;i++){
            id=UploadFile[i].id;
            if(ids.indexOf(id)!=-1){
                continue;
            }
            ids.push(id);
        }
        for(idi=0;idi<ids.length;idi++){
            id=ids[idi];
            fileobj=$("#"+id);
            vals=[];
            for(i=0;i<UploadFile.length;i++){
                if(id==UploadFile[i].id){
                    if(fileobj.attr("single")=="1"){
                        fileobj.val(UploadFile[i].url);
                    }
                    else{
                        vals.push(UploadFile[i].url);
                    }
                }
            }
            if(vals.length>0){
                fileobj.val(JSON.stringify(vals));
            }
        }
    }

    function uploaded(event,data,preview_id){
        url=data.response.url;
        if(url==""){
            alert("上传图片出现问题："+data.response.state);
        }
        else{
            id=data.response.id.replace("file_","");
            obj=$("#"+id);
            fileobj=$("#"+data.response.id);
            UploadFile.push({id:id,url:url,keyid:preview_id});
            changeValue();
            $("#token").val(data.response._token);
        }
    }


    function removePic(event, key , jqXHR, data)
    {
        for(i=0;i<UploadFile.length;i++){
            if(UploadFile[i].keyid==key){
                UploadFile.splice(i,1);
                break;
            }
        }
        changeValue();
    }

    function init_image(id){
        images.push(id);
        $("#"+id).fileinput(option_data).on("fileuploaded",uploaded);
        $("#"+id).on("filedeleted",removePic);
        $("#"+id).on("fileselect",function(e,f,lable){
            alert("成功选择新文件等待上传,请点击[上传]按钮,然后点击[提交修改]才可以生效");
        });

        $("#"+id).css("background","#FFF");
    }

    beforeFillForm=function(data){
        for(i=0;i<Editor.length;i++)//清理编辑器
        {
            $("#editor_value_"+Editor[i]).val('');
        }
        UploadFile=[];
        option_data.initialPreview=[];
        option_data.initialPreviewConfig=[];
        for(img in images){
            i=0;
            for(obj in data){
                i++;
                if(images[img].indexOf(obj)>0){
                    if(typeof data[obj] =="string"){
                        var d1=$.extend(true,{},option_data);
                        d1.overwriteInitial=true;
                        d1.initialPreview.push("<img src='"+data[obj]+"' style='height:60px;width:auto' class='file-preview-image'>");
                        d1.initialPreviewConfig.push({width:80,url: '/admin/content/deletefile',key:i,caption:"image_"+i,size:100})
                        UploadFile.push({id:obj,url:data[obj],keyid:i});
                        $("#"+images[img]).fileinput('destroy').fileinput(d1);
                    }
                    else{
                        if(Object.prototype.toString.call(data[obj])==='[object Array]'){
                            var d1=$.extend(true,{},option_data);
                            d1.overwriteInitial=false;
                            for(i=0;i<data[obj].length;i++){
                                d1.initialPreview.push("<img src='"+data[obj][i]+"' style='height:60px;width:auto' class='file-preview-image'>");
                                d1.initialPreviewConfig.push({width:80,url: '/admin/content/deletefile',key:i,caption:"image_"+i,size:100})
                                UploadFile.push({id:obj,url:data[obj][i],keyid:i});
                                $("#"+images[img]).fileinput('destroy').fileinput(d1);
                            }
                        }
                    }
                }
            }
        }
        if(UploadFile.length==0){
            for(img in images){
                file=$("#"+images[img]);
                var d1=$.extend(true,{},option_data);
                if(file.attr('single')==1){
                    d1.overwriteInitial=true;
                }
                else{
                    d1.overwriteInitial=false;
                }
                $("#"+images[img]).fileinput('destroy').fileinput(d1);
            }
        }
        changeValue();
    }
</script>
<div class="modal fade" tabindex="-1" role="dialog" id="model_new">
  <div class="modal-dialog" role="document" style="width:800px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogTitle"></h4>
      </div>
      <div class="modal-body">
          <form role="form" method="post" id="edit_form" action="{{$edit_url}}"  enctype="multipart/form-data">
              <input type="hidden" name="action" />
              <input type="hidden" id="token" name="_token" />
              <input type="hidden" name="id" />
          @if(count($inputs)>0)
              @foreach($inputs as $k=>$input)
              <div class="form-group">
                    @if($input['type']==DataTable::DEF_INTEGER ||   
                        $input['type']==DataTable::DEF_UINT ||
                        $input['type']==DataTable::DEF_MONEY)
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
                            <option value="{{$item['value']}}">{{$item['name']}}</option>
                        @endforeach
                        </select>
                    @elseif($input['type']==DataTable::DEF_MULTI_LIST)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        @foreach($input['const_list'] as $it)
                        <label><input type="checkbox" name="{{$input['name']}}_{{$it['value']}}" value="1">{{$it['name']}}</label>
                        @endforeach
                    @elseif($input['type']==DataTable::DEF_FLOAT)
                          <label for="exampleInputEmail1">{{$input['note']}}</label>
                          <input type="text" name="{{$input['name']}}" name="{{$input['name']}}" class="form-control" placeholder="{{$input['comment']}}" value="{{$input['default']}}">
                    @elseif($input['type']==DataTable::DEF_IMAGES)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <input id="kv-explorer_{{$input['name']}}" name="file_{{$input['name']}}" type="file" single="0" multiple="true">
                        <script type="text/javascript">init_image("kv-explorer_{{$input['name']}}");</script>
                        <input type="hidden" id="{{$input['name']}}" single="0" value="" name="{{$input['name']}}">
                    @elseif($input['type']==DataTable::DEF_IMAGE)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <input id="kv-explorer_{{$input['name']}}" name="file_{{$input['name']}}" single="1" type="file">
                        <script type="text/javascript">init_image("kv-explorer_{{$input['name']}}");</script>
                        <input type="hidden" id="{{$input['name']}}" single="1" value="" name="{{$input['name']}}">
                    @elseif($input['type']==DataTable::DEF_EDITOR)
                        <label for="exampleInputEmail1">{{$input['note']}}</label>
                        <script type="text/javascript">Editor.push("{{$input['id']}}");</script>
                        <script type="text/plain" id="editor_{{$input['id']}}" style="max-height:400px;overflow-y: auto;width:auto;height:240px;">
                            <p>这里我可以写一些输入提示</p>
                        </script>
                        <input type="hidden" name="{{$input['name']}}" id="editor_value_{{$input['id']}}">
                    @endif
              </div>
            @endforeach
              <!-- /.box-body -->
          @else
            <h5>无任何可以编辑的字段</h5>
          @endif
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="editBack" style="float:left;"> 上一条 </button>
        <button type="button" class="btn btn-default" style="float:left;" id="editNext"> 下一条 </button>
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
    $('#editNext').on('click',function(e){
      editNextData();
    });
    $('#editBack').on('click',function(e){
      editBackData();
    });
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

endFillForm=function(data)
{
    for(i=0;i<Editor.length;i++)
    {
        UM.getEditor("editor_"+Editor[i]).setContent($("#editor_value_"+Editor[i]).val());
    }
}

</script>
