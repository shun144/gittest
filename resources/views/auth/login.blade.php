{{-- @extends('adminlte::auth.auth-page', ['auth_type' => 'login']) --}}
@extends('auth.auth-page', ['auth_type' => 'login'])


@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif



{{-- @section('auth_header', __('adminlte::adminlte.login_message')) --}}
<meta name="description" content="LINE予約配信サービス管理画面">
@section('auth_body')
    
    <form action="{{ $login_url }}" method="post" autocomplete="off">
        @csrf

        <div class="input-group">
            <label for="login_id">ログインID</label>
            <input type="text" name="login_id" class="form-control @error('login_id') is-invalid @enderror"
                   value="{{ old('login_id') }}" placeholder="ログインIDを入力してください">

            @error('login_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>



        <div class="input-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="パスワードを入力してください">
                   {{-- placeholder="{{ __('adminlte::adminlte.password') }}"> --}}

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>




        {{-- <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}"> --}}
        <button type=submit>
            <span class="fas fa-sign-in-alt"></span>
            ログインする
            {{-- {{ __('adminlte::adminlte.sign_in') }} --}}
        </button>


    </form>

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('build/assets/login.css')}}">
@stop

@section('js')
    {{-- <script src="{{asset('vendor/adminlte/plugins/jquery/jquery.min.js')}}"></script> --}}
    <script src="{{asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script> --}}
@stop