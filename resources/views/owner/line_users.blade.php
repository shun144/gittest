@extends('adminlte::page')

@section('title','Dashboard')

@section('content_header')
    <h1>連携LINEユーザ一覧</h1>
@stop

@section('content')


  <div class="card mx-auto" style="width:70rem;">
    <div class="card-header">
      <a href="{{$reg_url}}" target="_blank" rel="noopener noreferrer">ユーザLINE連携ページ</a>
    </div>

    <div class="card-body">
      <table id="line_user_table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (array("操作","状態","LINE名","登録日時") as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($lines as $line)
          <tr>
            <td>
              <div class="row justify-content-around">
                <form action="{{route('store.del')}}" method="post" onSubmit="return confirmDelete(event)">
                  @csrf
                  <button type="submit" class="btn btn_del_store">
                    {{-- <input type="hidden" name="user_id" value={{$store->user_id}}>
                    <input type="hidden" name="store_id" value={{$store->store_id}}>
                    <input type="hidden" class="hid_store_name" value={{$store->name}}> --}}
                    <i class="fas fa-trash-alt text-muted"></i>
                  </button>
                </form>
              </div>
            </td>
            <td style="text-align:center">{{$line->is_valid == true ? '有効':'無効'}}</td>
            <td class="omit_td">{{$line->user_name}}</td>
            <td class="omit_td">{{$line->created_at}}</td>
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
  .btn_del_store:hover i{
    color: red !important;
  }

  .btn_edit_store:hover i{
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
  $('#line_user_table').DataTable({
    paging:true,
    lengthChange:false,
    searching:true,
    ordering:true,
    info:true,
    autoWidth: false,
    responsive:false,
    columnDefs:[
      { targets:0, width:60},
      { targets:1, width:60},
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
