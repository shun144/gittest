@extends('adminlte::master')

{{-- <nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TOP</a>
  </div>
</nav> --}}

<div class="d-flex h-100 align-items-start">
    <div class="card card-info mx-auto" style="width:45rem; margin:5rem">
        <div class="card-header h2">
            【{{$store->name}}】公式LINE連携
        </div>
        <div class="d-flex justify-content-center">
            <img class="card-img-top" src="{{ url(config('app.storage.register.image').'/line_notify.png')}}" alt="Card image cap" style="max-width:25rem; margin-top:1rem">
        </div>
        
        <div class="card-body mt-1">
            <div class="px-5">
                <p class="card-text">LINE連携ボタンをクリックすると↑のような画面が表示されます。<br>
                <span class="text-md font-weight-bold">1:1でLINE Notifyから通知を受け取る</span>を選択すると連携することができます。</p>
                <div class="mt-4 w-100 text-right">
                    <form action="{{ route('line.auth', ['url_name'=>$store->url_name]) }}" method="get" enctype="multipart/form-data">
                        <button type="submit" class="btn btn-success btn-lg">LINE 連携</button>
                        <input type="hidden" name="url_name" value="{{ $store->url_name}}" />
                        <input type="hidden" name="client_id" value="{{ $store->client_id}}" />
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- <div class="mx-auto" style="width:50rem; height:100%">

    <div class="card card-info">
        <div class="card-header h2">
            【{{$store->name}}】公式LINE連携
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <form action="{{ route('line.auth', ['url_name'=>$store->url_name]) }}" method="get" enctype="multipart/form-data">
                    <button type="submit" class="btn btn-success btn-lg">LINE 連携</button>
                    <input type="hidden" name="url_name" value="{{ $store->url_name}}" />
                    <input type="hidden" name="client_id" value="{{ $store->client_id}}" />
                </form>
            </div>
        </div>
    </div>

</div> --}}

{{-- <div class="container container-fluid mt-4">
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
</div> --}}

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