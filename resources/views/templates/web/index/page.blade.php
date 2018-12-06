@extends("templates.web.layouts.common")
@section('title','首页')
@section('page_css')
<link rel="stylesheet" href="/web/assets/css/news.min.css" />
@endsection
@section("content")
@include("templates.web.components.index_nav")
@include("templates.web.components.index_price_list")
@include("templates.web.components.index_position_large_icon")
@include("templates.web.components.index_content")
@include("templates.web.components.footer")
@endsection
