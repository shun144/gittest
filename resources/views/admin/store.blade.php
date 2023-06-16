@extends('adminlte::page')

@section('title','Dashboard')

@section('content_header')
    <h1>店舗情報一覧</h1>
@stop

@section('content')

  <div class="card">
    <div class="card-header">
      <a href="{{route('store.add.view')}}" class="btn btn-success">店舗追加</a>
      
      {{-- <div class="card-tools">
        <div class="input-group input-group-sm" style="width:150px;">
          <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
          <div class="input-group-append">
            <button type="submit" class="btn btn-default">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div> --}}

    </div>

    <div class="card-body">
      <table id="store_table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (array("操作","店舗名","店舗URL","ログインID","LINEサービスID","LINEサービスパスワード") as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($stores as $store)
          <tr>
            <td>
              <div class="row justify-content-around">
                <form action="{{route('store.del')}}" method="post" onSubmit="return confirmStoreDelete(event)">
                  @csrf
                  <button type="submit" class="btn btn_del">
                    <input type="hidden" name="user_id" value={{$store->user_id}}>
                    <input type="hidden" name="store_id" value={{$store->store_id}}>
                    <input type="hidden" class="hid_store_name" value={{$store->name}}>
                    <i class="fas fa-trash-alt text-muted"></i>
                  </button>
                </form>

                <form action="{{route('store.edit.view')}}" method="get">
                  <button type="submit" class="btn btn_edit">
                    <input type="hidden" name="store_id" value={{$store->store_id}}>
                    <i class="fas fa-edit text-muted"></i>
                  </button>
                </form>
              </div>
            </td>
            <td class="omit_td">{{$store->name}}</td>
            <td class="omit_td">{{$store->url_name}}</td>
            <td>{{$store->login_id}}</td>
            <td class="omit_td">{{$store->client_id}}</td>
            <td class="omit_td">{{$store->client_secret}}</td>
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
@vite(['resources/sass/component.scss'])

<style>



</style>

@stop

@section('js')
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
  @vite(['resources/js/component.js'])

<script>      
@if (session('flash_message'))
  $(function () {toastr.success('{{ session('flash_message') }}');});
@endif

function confirmStoreDelete(e){
  const del_store_name = e.submitter.querySelector('.hid_store_name').value
  const msg = del_store_name + ' を削除してよろしいですか？'
  if(window.confirm(msg)){return true;return true;}
  else{return false;}
};


$(function () {
  $.extend( $.fn.dataTable.defaults, { 
    language: {url: "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
  }); 
  $('#store_table').DataTable({
    paging:true,
    lengthChange:false,
    searching:true,
    ordering:true,
    info:true,
    autoWidth: false,
    responsive:false,
    columnDefs:[
      { targets:0, width:55},
      { targets:3, width:120},
    ],
  });
});

</script>
@stop

