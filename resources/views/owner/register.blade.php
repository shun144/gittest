@extends('adminlte::master')

<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TOP</a>
  </div>
</nav>

<div class="container container-fluid mt-4">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-title text-center">
                    <h2>公式LINE連携</h2>
                    <h3>{{$store->name}}</h3>
                </div>           
               
                <div class="card-body text-center">
                    <form action="{{ route('line.auth', ['url_name'=>$store->url_name]) }}" method="get" enctype="multipart/form-data">
                        <button type="submit" class="btn btn-success btn-lg">LINE 連携</button>
                        <input type="hidden" name="url_name" value="{{ $store->url_name}}" />
                        <input type="hidden" name="client_id" value="{{ $store->client_id}}" />
                    </form>
                </div>
        </div>
    </div>
</div>

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
    <script>
        @if (session('flash_message'))
            $(function () {
                toastr.success('{{ session('flash_message') }}');
            });
        @endif
    </script>
@stop