@extends("templates.web.layouts.common")
@section('title','首页')
@section('page_css')
<link rel="stylesheet" href="/web/assets/css/news.min.css" />
@endsection
@section("content")
@include("templates.web.components.index_nav")
    <!--===========layout-container================-->
    <div class="layout-container">
      <div class="page-header">
        <div class="am-container">
          <h1 class="page-header-title">{{$page_title}}</h1>
        </div>
      </div>

      <div class="breadcrumb-box">
        <div class="am-container">
          <ol class="am-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="am-active">{{$page_title}}</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="container">
        <div class="section--header">
            <h2 class="section--title">Latest News</h2>
            <p class="section--description">
                {{$description}}
            </p>
		</div>

        <div class="news-contaier">
          <div class="blog">
            <div class="am-g">
              @foreach($list as $item)
              <div class="am-u-lg-4 am-u-md-6  am-u-end">
                <div class="article">
                  <div class="article-img">
                    <img src="{{$item->cover}}" alt="{{$item->title}}" />
                  </div>
                  <div class="article-header">
                    <h2><a href="/a/{{$item->id}}" rel="">{{$item->title}}</a></h2>
                    <ul class="article--meta">
                      <li class="article--meta_item -date">{{$item->created_at}}</li>
                      <li class="article--meta_item comments">0 Views</li>
                    </ul>
                  </div>
                  <div class="article--content">
						        <p>{{$item->description}}</p>
				          </div>
                  <div class="article--footer">
  				          <a href="/a/{{$item->id}}" class="link">Read More</a>
  				        </div>
                </div>
              </div>
              @endforeach
            </div>
            <!-- pagination-->
            <ul class="am-pagination">
              <li class="am-disabled"><a href="#">&laquo;</a></li>
              <li class="am-active"><a href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
              <li><a href="#">&raquo;</a></li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </div>
@include("templates.web.components.footer")
@endsection
