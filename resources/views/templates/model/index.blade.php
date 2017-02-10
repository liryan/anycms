@extends('layouts.main')

@section('title', '测试标题')

@section('content')

<div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Hover Data Table</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example2" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>Rendering engine</th>
                <th>Browser</th>
                <th>Platform(s)</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              <tr>
                <th>Rendering engine</th>
                <th>Browser</th>
                <th>Platform(s)</th>
              </tr>
              </tfoot>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
<script src="/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
  $(function () {
    $("#example2").DataTable({
        "processing":true,
        "serverSide":true,
        "paging": true,
        "searching":false,
        "ajax":"/setting/models?data=ok",
        "columns":[
            {"data":"name"},
            {"data":"time"},
            {"data":"status"}
        ]
    }
    );
});
</script>
<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
          <form role="form" action="/setting/menumodify" method="post">
              {{ csrf_field() }}
              <div class="box-body">
              <div class="form-group">
                <label for="exampleInputEmail1">名字</label>
                <input type="text" name="name" class="form-control" placeholder="Enter ...">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">设置</label>
                <input type="text" name="setting" class="form-control" placeholder="Enter ...">
                <input type="text" name="order" class="form-control" value="0" placeholder="Enter ...">
                <input type="text" name="parentid" class="form-control" value="0" placeholder="Enter ...">
              </div>
              <div class="form-group">
                  <label>类型</label>
                      <select name="type" class="form-control">
                          <option value="1">栏目</option>
                          <option value="2">菜单</option>
                      </select>
                  </div>
              <div class="checkbox">
                <label>
              	<input type="checkbox"> 通过审核
                </label>
              </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">

              <button type="submit" class="btn btn-primary">Submit</button>
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
