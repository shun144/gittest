@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>スタッフ情報一覧</h1>
@stop

@section('content')

<section class="content">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        <div class="input-group input-group-sm" style="width: 150px;">
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
            <th>名前</th>
            <th>メールアドレス</th>
            <th>パスワード</th>
            <th>所属</th>
          </tr>
        </thead>
        <tbody>
        @foreach ($members as $member)
          <tr>
            <td>{{$member['name']}}</td>
            <td>{{$member['email']}}</td>
            <td>***********</td>
            <td>{{$member['store_name']}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</section>

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