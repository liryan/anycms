<form role="form" class="form-horizontal" method="post" id="search_form">
	<div class="form-group">
		<label class="col-sm-3 control-label">搜索项</label>
		<div class="col-sm-3"><select name="field" class="form-control">
		@foreach($fields as $row)
			<option value='{{$row['name']}}'>{{$row['note']}}</option>
		@endforeach
		</select></div>
		<div class="col-sm-3"><input type="text" name="keyword" class="form-control" placeholder="关键词" value=""></div>
		<div class="col-sm-3"><button type="button" class="btn btn-default" onclick="$('#search_form').submit()"> 搜索 </button></div>
	</div>
</form>
