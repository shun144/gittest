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
    
    <form action="{{ $login_url }}" method="post">
        @csrf

        {{-- LoginID field --}}
        <div class="input-group mb-3">
            <input type="text" name="login_id" class="form-control @error('login_id') is-invalid @enderror"
                   value="{{ old('login_id') }}" placeholder="ログインID" autofocus>

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

            {{-- <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div> --}}

            @error('login_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


        {{-- ConditionCheck field --}}
        <div id="input_terms_condition" class="input-group">
            <label for="input_condition_check">
                <input id="input_condition_check" type="checkbox" name="conditioncheck">        
                利用規約に同意する
            </label>
                   
            @error('conditioncheck')
                <span class="invalid-feedback @error('conditioncheck')is_check_invalid @enderror" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            @include('owner.modals.terms_condition')
            @include('owner.modals.privacy_policy')
        </div>
        {{-- <div class="terms_condition mt-2  mb-2" data-toggle="modal" data-target="#modal_terms_condition">利用規約</div>
        <div class="privacy_policy mb-2" data-toggle="modal" data-target="#modal_privacy_policy">プライバシーポリシー</div> --}}


        {{-- Login field --}}
        <div class="row mt-3">
            <div class="col-7">
                <div class="terms_condition mb-2" data-toggle="modal" data-target="#modal_terms_condition">利用規約</div>
                <div class="privacy_policy" data-toggle="modal" data-target="#modal_privacy_policy">プライバシーポリシー</div>
                {{-- <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div> --}}
            </div>

            <div class="col-5 align-self-end">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>

    </form>

@stop

{{-- @section('auth_footer')
    @if($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    @if($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif
@stop --}}

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('build/assets/login.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('build/assets/login.min.css')}}">

  {{-- <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
  <link rel="stylesheet" href="{{ asset('build/assets/component.min.css')}}"> --}}
  {{-- @vite(['resources/sass/component.scss']) --}}
@stop

@section('js')
    <script src="{{asset('vendor/adminlte/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    {{-- <script>
    $(function () {
        let $scrollBtn = $(".scroll-button");
        $(window).scroll(function(){
            console.log('test');
            let scrollTop = $(window).scrollTop();
            if(scrollTop > $(window).height()){
                console.log($(window).height())
                $scrollBtn.css("opacity", ".7");
            }else{
                $scrollBtn.css("opacity", "0");
            }
        });
        $scrollBtn.click(function(){
            $("html,body").animate({scrollTop: 0}, 500, "swing");
        });
    })
    </script> --}}


@stop