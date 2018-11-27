<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="/web/assets/css/amazeui.css" />
  <link rel="stylesheet" href="/web/assets/css/common.min.css" />
  @section('page_css')
  <link rel="stylesheet" href="/web/assets/css/index.min.css" />
  @show
</head>
<body>
  <div class="layout">
    @yield("content")
  </div>
  <script src="/web/assets/js/jquery-2.1.0.js" charset="utf-8"></script>
  <script src="/web/assets/js/amazeui.js" charset="utf-8"></script>
  <script src="/web/assets/js/common.js" charset="utf-8"></script>
</body>

</html>
