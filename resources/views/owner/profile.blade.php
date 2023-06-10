@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>プロフィール</h1>
@stop

@section('content')

{{-- <div class="container container-fluid">
  <div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">About Me</h3>
    </div>
    <div class="card-body">
      <ul class="todo-list" data-widget="todo-list">
        <li>
          <div class="icheck-primary d-inline ml-2">
            <strong><i class="fas fa-map-marker-alt mr-1"></i>名前</strong>
            <span class="text-muted">{{$profile[0]['name']}}</span>  
          </div>
          <div class="tools">
            <i class="fas fa-edit"></i>
            <i class="fas fa-trash-o"></i>
          </div>
        </li>
        <li>
          <div class="icheck-primary d-inline ml-2">
            <strong><i class="fas fa-map-marker-alt mr-1"></i>所属</strong>
            <span class="text-muted">{{$profile[0]['store_name']}}</span>  
          </div>
          <div class="tools">
            <i class="fas fa-edit"></i>
            <i class="fas fa-trash-o"></i>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div> --}}


<div class="container container-fluid">
  <div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">About Me</h3>
    </div>
    <div class="card-body">
      <ul>
        <li>
          <strong><i class="fas fa-fw fa-user mr-1"></i>名前</strong>
          <p class="text-muted">{{$profile[0]['name']}}</p>
          <hr>
        </li>
        <li>
          <strong><i class="fas fa-book mr-1"></i>所属</strong>
          <p class="text-muted">{{$profile[0]['store_name']}}</p>  
          <hr>
        </li>
        <li>
          <strong><i class="far fa-file-alt mr-1"></i> 権限</strong>
          <p class="text-muted">管理者</p>
        </li>
      </ul>

    </div>
  </div>
</div>





{{-- <div class="container container-fluid">
  <div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">About Me</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <strong><i class="fas fa-map-marker-alt mr-1"></i>名前</strong>
      <p class="text-muted">{{$profile[0]['name']}}</p>
      <hr>
      <strong><i class="fas fa-book mr-1"></i>所属</strong>
      <p class="text-muted">{{$profile[0]['store_name']}}</p>  
      <hr>
      <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
      <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
    </div>
  </div>
</div> --}}




 @stop

@section('css')
    {{-- ページごとCSSの指定
    <link rel="stylesheet" href="/css/xxx.css">
    --}}
@stop


@section('js')
<script>
  console.log('店舗情報')
</script>
@stop