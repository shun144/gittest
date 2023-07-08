@extends('adminlte::page')

@section('title','ログファイル一覧')

@section('content_header')
    <h1>ログファイル一覧</h1>
@stop

@section('content')
<div class="mx-auto pb-5" style="width:40rem">
  <div class="card">
    {{-- <div class="card-header">
      <a href="{{route('store.add.view')}}" class="btn btn-success">店舗追加</a>
    </div> --}}

    <div class="card-body">
      <table id="log_table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (array("DL","ログファイル名") as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>

          @if (isset($logFiles))
            @foreach ($logFiles as $logFile)
            <tr>          
              <td>
                <div class="row justify-content-around">
                  <a class="fas fa-download text-muted" href="{{$logFile}}" download></a>
                </div>
              </td>
              <td class="omit_text">{{basename($logFile)}}</td>            
            </tr>
            @endforeach
          @endif

        </tbody>

      </table>
    </div>
  </div>
</div>


 @stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/component.min.css')}}">
{{-- @vite(['resources/sass/component.scss']) --}}

<style>
</style>

@stop

@section('js')
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
  {{-- @vite(['resources/js/component.js']) --}}

  <script>
  @if (isset($error_flushMsg))
    $(function () {toastr.error('{{ $error_flushMsg }}');});
  @endif

  $(function () {
    $.extend( $.fn.dataTable.defaults, { 
      language: {url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
    }); 
    $('#log_table').DataTable({
      paging:true,
      lengthChange:false,
      searching:true,
      ordering:true,
      info:false,
      autoWidth: false,
      responsive:false,
      columnDefs:[
        { targets:0, width:50}
      ],
      // drawCallback: function(){
      //   $(".dataTables_info").appendTo("#store_table_wrapper>.row:first-of-type>div:first-of-type");
      // },
    });
  });
  </script>

@stop

