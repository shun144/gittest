@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>動画管理</h1>
@stop

@section('content')

<div class="row justify-content-center">
  <div class="col-md-10 mb-5">
    <form action="{{ route('owner.movie.add') }}" enctype="multipart/form-data" method="post">
      {{ csrf_field() }}
      <input type="file" name="movie"><p>
      <input type="submit" value="動画をアップロード">
    </form>
  </div>





  <div class="col-md-10">
    <table id="movie_table" class="table table-striped table-bordered" style="table-layout:fixed;">
      <thead>
        <tr>
          <th class="text-center">URL</th>
          <th class="text-center">タイトル</th>
          <th class="text-center">詳細</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>test</th>
          <th>test</th>
          <th>test</th>
        </tr>
        <tr>
          <th>test</th>
          <th>test</th>
          <th>test</th>
        </tr>

        {{-- @if(isset($posts[0]))
          @foreach ($posts as $post)
            <tr>
              <td class="omit_text">{{$post->action_date}}</td>
              <td class="omit_text">{{$post->add_cnt}}</td>
              <td class="omit_text">{{$post->cancell_cnt}}</td>
            </tr>
          @endforeach
        @endif --}}


      </tbody>
    </table>
  </div>
</div>

@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('build/assets/graph.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>

<script src="{{ asset('build/assets/movie.js')}}"></script>
{{-- <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script> --}}

<script>
</script>

@stop