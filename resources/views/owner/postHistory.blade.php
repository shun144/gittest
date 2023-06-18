@extends('adminlte::page')

@section('title','Dashboard')

@section('content_header')
    <h1>配信履歴一覧</h1>
@stop

@section('content')


  <div class="card mx-auto">

    <div class="card-body">
      <table id="history_table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (["参照","状態","配信開始","配信完了","タイトル","内容","画像","エラー"] as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($posts as $post)
          <tr>
            <td class="d-flex justify-content-center">
              <form action="{{route('owner.history.info')}}" method="get">
                <button type="submit" class="btn btn_show">
                  <input type="hidden" name="history_id" value={{$post->id}}>
                  <i class="fas fa-eye text-muted"></i>
                </button>
              </form>
            </td>
            <td class="omit_td">{{$post->status}}</td>
            <td class="omit_td">{{$post->start_at}}</td>
            <td class="omit_td">{{$post->end_at}}</td>
            <td class="omit_td">{{$post->title}}</td>
            <td class="omit_td">{{$post->content}}</td>
            <td class="omit_td">{{$post->img_url == Null ? '無し' : '有り'}}</td>
            <td class="omit_td">{{$post->err_info}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
 @stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">

<style>
  .btn_show:hover i{
    color: blue!important;
  }

  td {
    vertical-align: middle !important;
  }
  .omit_td {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  
</style>

@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>

<script>      
@if (session('flash_message'))
  $(function () {toastr.success('{{ session('flash_message') }}');});
@endif

function confirmDelete(e){
  const del_store_name = e.submitter.querySelector('.hid_store_name').value
  const msg = del_store_name + ' を削除してよろしいですか？'
  if(window.confirm(msg)){return true;return true;}
  else{return false;}
};


$(function () {
  $.extend( $.fn.dataTable.defaults, { 
    language: {url: "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
  }); 
  $('#history_table').DataTable({
    paging:true,
    lengthChange:false,
    searching:true,
    ordering:true,
    info:true,
    autoWidth: false,
    responsive:false,
    columnDefs:[
      { targets:0, width:35},
      { targets:1, width:40},
      { targets:2, width:115},
      { targets:3, width:115},
      { targets:4, width:120},
      { targets:6, width:40},
      { targets:7, width:120},
    ],
  }); 
});

</script>
@stop





{{-- @extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>連携LINEユーザ一覧</h1>
@stop

@section('content')


  <div class="card">
    <div class="card-header">
      
      <div class="card-tools">
        <div class="input-group input-group-sm" style="width:150px;">
          <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
          <div class="input-group-append">
            <button type="submit" class="btn btn-default">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <table id="members" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>LINEユーザ名</th>
            <th>状態</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($lines as $line)
          <tr>
            <td>{{$line['user_name']}}</td>
            <td>{{$line['is_valid'] == true ? '有効':'無効'}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>


 @stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
      $('#members').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    })
</script>
@stop
 --}}
