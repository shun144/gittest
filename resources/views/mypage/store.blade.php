@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>店舗情報一覧</h1>
@stop

@section('content')


  <div class="card">
    <div class="card-header">
      <a class="btn btn-success" data-toggle="modal" data-target="#ModalAddStore">
        <span style="color:white">店舗追加</span>
      </a>
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
            <th>店舗名</th>
            <th>住所</th>
            <th>電話番号</th>
            <th>LINEサービスID</th>
            <th>LINEサービスパスワード</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($stores as $store)
          <tr>
            <td>{{$store['name']}}</td>
            <td>{{$store['addr']}}</td>
            <td>{{$store['tel']}}</td>
            <td>{{$store['client_id']}}</td>
            <td>***********************</td>
            {{-- <td>{{$store['client_secret']}}</td> --}}
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @include('admin.modal.add_store')


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

