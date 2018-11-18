@extends('layouts.front')
@section('title', '最美唐诗')
@section('content')

<div class="row">
{{foreach($datas as $row)}}
<div class="col-md-6">
	<div class="box box-success">
		<div class="box-header with-border">
		  <h2 class="box-title"><a href="/index/detail?id={{$row->id}}"><strong>{{$row->title}}</strong></a></h2>
		  <div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse">
		      <i class="fa fa-minus"></i>
			</button>
		  </div>
		</div>	
		<div class="box-body">
			<p class="text-red">
				[{{$row->age}}] <a href='/detail?id={{$row->id}}' style="color:#FFF">{{$row->author}}</a>
			</p>
			<p class="text-muted">
				{{$row->text}}
			</p>
		</div>
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		  <div class="box-tools pull-right">
			<span data-toggle="tooltip" title="赞一下" class="badge bg-yellow">
			 <a href="javascript:favit()" style="color:#FFF"><i class="fa fa-thumbs-o-up"></i> <span id="">3</span></a>
			</span>
			<span data-toggle="tooltip" title="详细信息" class="badge bg-yellow">
			 <a href="javascript:show_comment()" style="color:#FFF">译文</a>
			</span>
		  </div>
		</div>	
	</div>
</div>
<div id="comment_id" class="col-md-6" style="display:none">
</div>
{{/foreach}}
<!-- start -->
<div id="means" style="display:none">
  <div class="box box-widget">
	<div class="box-header with-border">
	<span class="username"><a href="#">原文释义</a></span><br>
	<span class="description"></span>
	  <div class="box-tools">
		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		</button>
		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	  </div>
	</div>
	<div class="box-body">
	  <p>
		<!--[data.message]-->
	  </p>
	  <button type="button" class="btn btn-default btn-xs"><i class="fa fa-share"></i> Share</button>
	  <button type="button" class="btn btn-default btn-xs"><i class="fa fa-thumbs-o-up"></i> Like</button>
	  <span class="pull-right text-muted">127 likes - 3 comments</span>
	</div>
  </div>
</div>
<!-- end -->
</div>
<script type="text/javascript">
	function compile(html,json)
	{
		for(var p in json){
			html=html.replace("<!--[data."+p+"]-->",json[p]);
		}
		return html;
	}
	function show_comment()
	{
		html=compile($("#means").html(),{message:'liruiyan'});
		$("#cc").html(html);
		$("#cc").css('display','block');
	}
</script>
@endsection
