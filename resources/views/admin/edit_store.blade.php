
@extends('adminlte::page')

@section('title','店舗情報編集')

@section('content_header')
    <h1>店舗情報編集</h1>
@stop

@section('content')


<div class="mx-auto pb-5" style="width:60rem">

  <div class="text-left">
    <a href="{{route('admin.store')}}" class="h5">
        <i class="fas fa-arrow-left"></i>
        <span>店舗情報一覧に戻る</span>
    </a>
  </div>
  <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title">店舗情報編集フォーム</h3>
    </div>
    <div class="card-body">
      <form id="formEditStore" action="{{ route('store.edit') }}" method="post" enctype="multipart/form-data" onSubmit="return confirmEdit(event)">
        @csrf
        <input type="hidden" name='user_id' value="{{$store->user_id}}">
        <input type="hidden" name='store_id' value="{{$store->store_id}}">

        <div class="form-group mb-5">
          <label for="store_name">店舗名</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="store_name" name="name" 
          value="{{ old('name',$store->name) }}" maxlength="{{config('field.store.name.max')}}">
          <div class="invalid-feedback">@error('name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.name.max')}}</small>
        </div>

        <div class="form-group mb-5">
          <label for="store_url_name">店舗アルファベット名</label>
          <input type="text" class="form-control @error('url_name') is-invalid @enderror" id="store_url_name" 
          name="url_name" value="{{ old('url_name',$store->url_name) }}" maxlength="{{config('field.store.url_name.max')}}">
          <div class="invalid-feedback">@error('url_name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.url_name.max')}} / 入力例:tempoa, shop01</small>
        </div>

        <div class="form-group mb-5">
          <label for="login_id">ログインID</label>
          <input type="text" class="form-control @error('login_id') is-invalid @enderror" id="login_id" 
          name="login_id" value="{{ old('login_id',$store->login_id) }}" maxlength="{{config('field.user.login_id.max')}}">
          <div class="invalid-feedback">@error('login_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_id.min')}}-{{config('field.user.login_id.max')}}</small>
        </div>

        <div class="form-group mb-5">
          <label for="login_password">ログインパスワード(変更する場合チェックボックスをONにしてください)</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <input type="checkbox" name="is_change_password"
                onclick="clickCheckChangePassword(event)" style="cursor:pointer"
                {{ empty(old("login_password")) ? '':'checked'}}>
              </div>
            </div>
            <input type="text" class="form-control @error('login_password') is-invalid @enderror" id="login_password" name="login_password" value="{{ old('login_password')}}" {{ empty(old("login_password")) ? 'readonly':''}}  maxlength="{{config('field.user.login_password.max')}}">
            <div class="invalid-feedback">@error('login_password'){{ $message }}@enderror</div>  
          </div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_password.min')}}-{{config('field.user.login_password.max')}}</small>
        </div>

        <div class="form-group mb-5">
          <label for="client_id">LINEサービスID</label>
          <input type="text" class="form-control @error('client_id') is-invalid @enderror" id="client_id" 
          name="client_id" value="{{ old('client_id',$store->client_id) }}">
          <div class="invalid-feedback">@error('client_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient IDを入力してください</small>
        </div>

        <div class="form-group mb-3">
          <label for="client_secret">LINEサービスパスワード</label>
          <input type="text" class="form-control @error('client_secret') is-invalid @enderror" id="client_secret" 
          name="client_secret" value="{{ old('client_secret',$store->client_secret) }}">
          <div class="invalid-feedback">@error('client_secret'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient Secretを入力してください</small>
          
        </div>

      </form>
    </div>
    <div class="card-footer bg-transparent">
      <button form="formEditStore" type="submit" class="btn btn-primary">更新</button>
    </div>
  </div>
</div>





@stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
<script>
@if (session('flash_message'))
  $(function () {toastr.success('{{ session('flash_message') }}');});
@endif

function confirmEdit(){
  const msg = '店舗情報を更新してよろしいですか？'
  if(window.confirm(msg))
  {
    return true;
  }
  else{
    return false;
  }
};

function clickCheckChangePassword(e){
  const check = e.target.checked
  const inputChgPass = document.getElementById('login_password');
  inputChgPass.readOnly = !check;
  inputChgPass.value = '';
}
</script>
@stop