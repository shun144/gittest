@extends('adminlte::page')

@section('title','連携LINEユーザ一覧')

@section('content_header')
  <h2>連携LINEユーザ一覧</h2>
@stop

@section('content')

  <div id="line_user_status_loader">
    <div class="loading">
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
    </div>
  </div>

  <div class="lineuser">

    <div class="entry-url">
      <label for="entry-url-form">LINE連携ページURL(読取)</label>
      <div class="input-group">
        <span class="btn btn-outline-secondary" onclick="copyToClipboard()"><i class="far fa-copy"></i></span>
        <input id="input_reg_url" type="text" class="form-control" name="entry-url-form" value="{{isset($reg_url) ? $reg_url:'―'}}" readonly>
      </div>
    </div>

    <div class="taikai-check">
      <button form="formUpdLineUser" type="submit" class="btn btn-taikai">退会確認</button>
      <form id="formUpdLineUser" action="{{ route('line_users.upd.status') }}" method="get" onSubmit="return updateLineUserStatus(event)">
        @csrf
      </form>
    </div>

    <div class="lineuser-table">
      <table id="line_user_table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (["状態","登録日時","LINE名"] as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>

        <tbody>

          @if(isset($lines))
            @foreach ($lines as $line)
            <tr>
              <td class="omit_text">
                <form action="{{route('line_users.edit')}}" method="post" onSubmit="return confirmEditLineUser(event)">
                  @csrf
                  <button type="submit" class="btn btn_edit">
                    @if ($line->is_valid == 1)
                      <span class="text-blue">有効</span>
                    @else
                      <span class="text-red">無効</span>
                    @endif
                    <input type="hidden" name="line_user_id" value={{$line->id}}>
                    <input type="hidden" name="new_valid" value={{$line->is_valid == 1 ? 0 : 1}}>
                    <input type="hidden" class="hid_line_user_name" value={{$line->user_name}}>
                  </button>
                </form>
              </td>
              <td class="omit_text">{{$line->created_at}}</td>
              <td class="omit_text">{{$line->user_name}}</td>
            </tr>
            @endforeach

          @endif
        </tbody>
      </table>
    </div>



  </div>  
  
 @stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/lineuser.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
<script src="{{ asset('build/assets/lineUser.js')}}"></script>
{{-- <script src="{{ asset('vendor/popper/popper.min.js')}}"></script> --}}

<script>
@if (isset($get_lineuser_error_flushMsg))
  $(function () {toastr.error('{{ $get_lineuser_error_flushMsg }}');});
@endif

@if (session('edit_lineuser_success_flushMsg'))
  $(function () {toastr.success('{{ session('edit_lineuser_success_flushMsg') }}');});
@endif

@if (session('edit_lineuser_error_flushMsg'))
  $(function () {toastr.error('{{ session('edit_lineuser_error_flushMsg') }}');});
@endif

function copyToClipboard() {
  var copyTarget = document.getElementById("input_reg_url");
  copyTarget.select();
  document.execCommand("Copy");
}

function confirmEditLineUser(e){
  const name = e.submitter.querySelector('.hid_line_user_name').value
  const new_valid = e.submitter.querySelector('input[name="new_valid"]').value
  let msg = 'LINE名「' + name + '」'
  if (new_valid == 1){
    msg = `${msg}を有効化してよろしいですか？`;
  }
  else {
    msg = `${msg}を無効化してよろしいですか？`;
  }

  if(window.confirm(msg)){
    return true;
  } else {
    return false;
  }
};



$(function () {
  $.extend( $.fn.dataTable.defaults, { 
    language: {url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
  }); 
  $('#line_user_table').DataTable({
    paging:true,
    lengthChange:false,
    searching:true,
    ordering:true,
    info:true,
    autoWidth: false,
    responsive:false,
    order: [[1,"desc"]],
    columnDefs:[
      { targets:0, width:'6%'},
      { targets:1, width:'25%'}, 
    ],

    // columnDefs:[
    //   { targets:0, width:50},
    //   { targets:1, width:180}, 
    // ],
    // drawCallback:() => {
    //   $(".dataTables_info").appendTo("#line_user_table_wrapper>.row:first-of-type>div:first-of-type");
    // }
  });
});


</script>
@stop

