
@extends('adminlte::master')

<div class="line_register">
    <div class="mx-auto">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
            <div class="row w-100">
                <div class="col-8 px-0 d-flex" >
                    <h2 class="align-self-center">{{$store->name}}</h2>
                </div>
                
                <div class="col-4 px-0 d-flex">
                    <button form='lineAuthForm' type="submit" class="btn btn-success align-self-center ml-auto">LINE連携</button>
                </div>
            </div>
        </nav>

        <div>
            <div class="card card-light mx-auto shadow-none">
                <div class="card-body">
                    <form class="mb-0" id='lineAuthForm' action="{{ route('line.auth', ['url_name'=>$store->url_name]) }}" method="get" enctype="multipart/form-data">                    
                        <input type="hidden" name="url_name" value="{{ $store->url_name}}" />
                        <input type="hidden" name="client_id" value="{{ $store->client_id}}" />
                    </form>

                    <div class="mx-1">

                        <div class="description">
                            公式LINE連携ページ
                        </div>

                        <div class="row section">
                            <h3>LINE連携方法</h3>
                            <div class="mb-4">
                                <p>右上のLINE連携ボタンをクリックすると↓のような画面が表示されます。
                                    <span>1:1でLINE Notifyから通知を受け取る</span>を選択し、 <span>同意して連携する</span>をクリックしてください。
                                </p>
                            </div>
                            <div class="d-flex justify-content-center w-100">
                                <img class="card-img-top" src="{{ url(config('storage.user.image.register').'/line_notify.png')}}" alt="Card image cap">
                            </div>
                        </div>

                        <div class="row section">
                            <h3>LINE連携時のログイン</h3>
                            <div class="mb-4">
                                <p><span>LINE連携方法</span>を実行し↓のような画面が出る場合は、メールアドレスとパスワードを入力し、ログインしてください。</p>
                            </div>
                            <div class="d-flex justify-content-center w-100">
                                <img class="card-img-top" src="{{ url(config('storage.user.image.register').'/line_login.png')}}" alt="Card image cap">
                            </div>
                        </div>

                        <div class="row section">
                            <h3>LINE通知について</h3>
                            <div class="mb-4">
                                <p>LINE Notifyの仕組みを利用しているため、実際のLINE通知は↓のようにLINE Notifyから来ます。</p>
                            </div>
                            <div class="d-flex justify-content-center w-100">
                                <img class="card-img-top" src="{{ url(config('storage.user.image.register').'/notify.png')}}" alt="Card image cap">
                            </div>
                        </div>


                        <div class="row section_last">
                            <h3 class="regi_info_title">LINE連携の解除</h3>
                            <div class="mb-4">
                                <p>LINE連携を解除されたい場合は、<a href="https://notify-bot.line.me/my/">LINE Notifyのマイページ</a>へアクセスし、<span>解除</span>をクリックしてください。</p>
                                
                            </div>
                            <div class="d-flex justify-content-center w-100">
                                <img class="card-img-top register_image" src="{{ url(config('storage.user.image.register').'/lift.png')}}" alt="Card image cap">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
    <link rel="stylesheet" href="{{ asset('build/assets/component.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('build/assets/component.min.css')}}"> --}}
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
    <script>
        @if (session('success_flash_message'))
        $(function () {toastr.success('{{ session('success_flash_message') }}');});
        @endif
        @if (session('error_flash_message'))
        $(function () {toastr.success('{{ session('error_flash_message') }}');});
        @endif
    </script>
@stop

