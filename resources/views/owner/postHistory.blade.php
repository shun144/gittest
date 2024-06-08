@extends('adminlte::page')

@section('title','配信履歴一覧')


@section('content_header')
  <h2>配信履歴一覧</h2>
@stop

@section('content')

<div class="posthistory">

  <div class="posthistory-table">
    <table id="history_table" class="table table-striped table-bordered" style="table-layout:fixed;">
      <thead>
        <tr>
          @foreach (["参照","状態","配信日時","内容","画像","エラー"] as $col)
          <th class="text-center">{{$col}}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
  
      @if(isset($posts[0]))
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
            <td class="omit_text">{{$post->status}}</td>
            <td class="omit_text ws-wrap">{{$post->start_at}}</td>
            <td class="omit_text">{{$post->content}}</td>
            <td class="omit_text">{{$post->img_url == Null ? '無し' : '有り'}}</td>
            <td class="omit_text">{{$post->err_info}}</td>
          </tr>
        @endforeach
      @endif
      
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
<link rel="stylesheet" href="{{ asset('build/assets/posthistory.css')}}">

@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}" defer></script>
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
    language: {url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
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
      { targets:0, width:'3%'},
      { targets:1, width:'5%'},
      { targets:2, width:'12%'},
      { targets:4, width:'3%'},
      { targets:5, width:'12%'},

      // { targets:0, width:35},
      // { targets:1, width:35},
      // { targets:2, width:160},
      // // { targets:3, width:200},
      // { targets:4, width:40},
      // { targets:5, width:200},


    ],
    // drawCallback: function(){
    //   $(".dataTables_info").appendTo("#history_table_wrapper>.row:first-of-type>div:first-of-type");
    // }
  }); 
});

</script>
@stop
