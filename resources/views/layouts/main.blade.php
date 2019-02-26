<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/adminlte/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/adminlte/dist/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/adminlte/dist/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/adminlte/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/adminlte/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="/adminlte/plugins/datatables/dataTables.bootstrap.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/adminlte/plugins/iCheck/flat/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="/adminlte/plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->

  <!-- jQuery 2.2.3 -->
  <script src="/adminlte/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="/adminlte/plugins/jQueryUI/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button);
  </script>
  <!-- Bootstrap 3.3.6 -->
  <script src="/adminlte/bootstrap/js/bootstrap.min.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="/admin/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">后台数据管理系统</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">导航开关</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
          <a>目前数据库:{{$hostname}}</a>
          </li>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{$avatar}}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{$username}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{$avatar}}" class="img-circle" alt="User Image">
                <p>
                  {{$username}}
                  <small>管理员</small>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="/admin/personal/profile" class="btn btn-default btn-flat">个人信息</a>
                </div>
                <div class="pull-right">
                  <a href="/admin/logout" class="btn btn-default btn-flat">退出</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{$avatar}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{$username}}</p>
          <a href="/admin/logout"><i class="fa fa-circle text-success"></i>退出</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">导航</li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>个人面板</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/admin/personal/welcome"><i class="fa fa-coffee"></i>工作台</a></li>
            <li><a href="/admin/personal/setting"><i class="fa fa-cog"></i>设置</a></li>
            <li><a href="/admin/personal/profile"><i class="fa fa-user"></i>账户</a></li>
          </ul>
        </li>
        @if($admin==1)
        <li class="treeview">
          <a href="#">
            <i class="fa fa-gears"></i> <span>系统面板</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @foreach($sys_menus as $menu)
            <li><a href="/admin{{$menu['note']}}"><i class="{{$menu['setting']}}"></i>@if(isset($menu['name'])){{$menu['name']}}@endif</a></li>
            @endforeach
          </ul>
        </li>
        @endif
        <li class="treeview">
          <a href="#">
            <i class="fa fa-database"></i> <span>内容管理</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              @foreach($categories as $row)
                @if($row['subdata']=='>')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-table"></i> <span>@if(isset($row['name'])){{$row['name']}}@endif</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                @elseif($row['subdata']=='|')
                    @if(!isset($row['id']))
                    @else
                    <li><a href="/admin/content?catid={{$row['id']}}"><i class="fa fa-table"></i>@if(isset($row['name'])){{$row['name']}}@endif</a></li>
                    @endif
                @elseif($row['subdata']=='<')
                    </ul>
                </li>
                @endif
              @endforeach
          </ul>
        </li>
        <!-- 内容管理结束-->
        <!-- 统计管理开始-->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i> <span>数据统计</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              @foreach($statmenus as $row)
                @if($row['subdata']=='>')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-table"></i> <span>@if(isset($row['name'])){{$row['name']}}@endif</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                @elseif($row['subdata']=='|')
                    @if(!isset($row['id']))
                    @else
                    <li><a href="/admin/stat/detail?statid={{$row['id']}}"><i class="fa fa-pie-chart"></i>@if(isset($row['name'])){{$row['name']}}@endif</a></li>
                    @endif
                @elseif($row['subdata']=='<')
                    </ul>
                </li>
                @endif
              @endforeach
          </ul>
        </li>
        <!-- 统计管理结束-->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-gg-circle"></i> <span>扩展功能</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              @foreach($user_menus as $row)
                @if($row['subdata']=='>')
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-table"></i> <span>{{$row['name']}}</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                @elseif($row['subdata']=='|')
                    @if(!isset($row['id']))
                    @else
                    <li><a href="@if(strpos(strtolower($row['note']),'http')!==false){{$row['note']}}@else/admin/ext/{{$row['note']}}@endif"><i class="{{$row['setting']}}"></i>{{$row['name']}}</a></li>
                    @endif
                @elseif($row['subdata']=='<')
                    </ul>
                </li>
                @endif
              @endforeach
          </ul>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        主导航
      </h1>
      <ol class="breadcrumb">
        <li><a href="/admin/index"><i class="fa fa-dashboard"></i> 主页</a></li>
        @foreach($breadcrumb as $row)
        <li><a href="{{$row['url']}}">{{$row['name']}}</a></li>
        @endforeach
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        @yield('content')
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 0.0.1beta
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">admlite</a>.</strong> All rights
    reserved.
  </footer>

  <div class="control-sidebar-bg"></div>
</div>
<script type="text/javascript">
    var url="{{$path}}";
    root=$(".sidebar");
    compare=function(node){ //node is li
        var obj=node.children("a")
        if(obj.length==0){
            return false;
        }
        var cururl=$(obj).attr("href");
        if(url.indexOf(cururl) >= 0){
            return true;
        }
        var next=node.children("ul");
        if(next.length==0)
            return false;
        var clds=next.children("li");
        for(var i=0;i<clds.length;i++){
            if(compare($(clds[i]))){
                $(next).addClass("menu-open");
                $(next).css("display","block");
                return true;
            }
        }
        return false;
    }
    
    function compareNode(node){
      var first=node.children("ul");
      if(first.length==0)
        return;
      var childs=$(first[0]).children("li");
      if(childs.length==0)
        return;
      for(var c=0;c<childs.length;c++){
        compareNode($(childs[c]));
        if(compare($(childs[c]))){
            $(childs[c]).attr("class","active");
        }
      }
    }

    compareNode(root);
    
</script>
<!-- ./wrapper -->
<!-- AdminLTE App -->
<script src="/adminlte/dist/js/app.min.js"></script>
</body>
</html>
