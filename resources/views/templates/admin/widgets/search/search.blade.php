<form role="form" class="form-horizontal" method="post" id="search_form">
	<div class="form-group">
		<label class="col-sm-2 control-label">搜索项</label>
		<div class="col-sm-2"><select name="field" class="form-control">
		@foreach($fields as $row)
			<option value='{{$row['name']}}'>{{$row['note']}}</option>
		@endforeach
		</select></div>
        <div class="col-sm-1">
            <select name="math" class="form-control" >
                <option value="=">等于</option>
                <option value="<">小于</option>
                <option value=">">大于</option>
                <option value="<>">不等于</option>
                <option value="between">范围(两个数字，用,号隔开)</option>
                <option value="like">含有字符</option>
            </select>
        </div>
		<div class="col-sm-2"><input type="text" name="keyword" class="form-control" placeholder="关键词" value=""></div>
		<div class="col-sm-2"><button type="button" class="btn btn-default" onclick="$('#search_form').submit()"> 搜索 </button></div>
	</div>
</form>
