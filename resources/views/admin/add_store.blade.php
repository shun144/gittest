
@extends('adminlte::page')

@section('title','Dashboard')

@section('content_header')
    <h1>店舗登録</h1>
@stop

@section('content')

  <div class="card card-primary mx-auto" style="width:60rem;">
    <div class="card-header">
      <h3 class="card-title">店舗登録フォーム</h3>
    </div>

    <form action="{{ route('store.add') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        
        
        <div class="form-group mb-5">
          <label for="store_name">店舗名</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="store_name" name="name" 
          value="{{ old('name')}}" maxlength="{{config('field.store.name.max')}}">
          <div class="invalid-feedback">@error('name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.name.max')}}</small>
        </div>

        <div class="form-group mb-5">
          <label for="store_url_name">店舗アルファベット名</label>
          <input type="text" class="form-control @error('url_name') is-invalid @enderror" id="store_url_name" 
          name="url_name" value="{{ old('url_name')}}" maxlength="{{config('field.store.url_name.max')}}">
          <div class="invalid-feedback">@error('url_name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.url_name.max')}} / 入力例:tempoa, shop01</small>
        </div>

        <div class="form-group mb-5">
          <label for="login_id">ログインID</label>
          <input type="text" class="form-control @error('login_id') is-invalid @enderror" id="login_id" 
          name="login_id" value="{{ old('login_id')}}" maxlength="{{config('field.user.login_id.max')}}">
          <div class="invalid-feedback">@error('login_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_id.min')}}-{{config('field.user.login_id.max')}}</small>
        </div>


        <div class="form-group mb-5">
          <label for="login_password">パスワード</label>
          <input type="text" class="form-control @error('login_password') is-invalid @enderror" id="login_password" 
          name="login_password" value="{{ old('login_password')}}" maxlength="{{config('field.user.login_password.max')}}">
          <div class="invalid-feedback">@error('login_password'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_password.min')}}-{{config('field.user.login_password.max')}}</small>
        </div>

        {{-- <div class="form-group mb-5">
          <label for="password">パスワード</label>
          <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" 
          name="password" value="{{ old('password')}}" maxlength="{{config('field.user.password.max')}}">
          <div class="invalid-feedback">@error('password'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.password.min')}}-{{config('field.user.password.max')}}</small>
        </div> --}}
      
        <div class="form-group mb-5">
          <label for="client_id">LINEサービスID</label>
          <input type="text" class="form-control @error('client_id') is-invalid @enderror" id="client_id" 
          name="client_id" value="{{ old('client_id')}}">
          <div class="invalid-feedback">@error('client_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient IDを入力してください</small>
        </div>

        <div class="form-group mb-5">
          <label for="client_secret">LINEサービスパスワード</label>
          <input type="text" class="form-control @error('client_secret') is-invalid @enderror" id="client_secret" 
          name="client_secret" value="{{ old('client_secret')}}">
          <div class="invalid-feedback">@error('client_secret'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient Secretを入力してください</small>
          
        </div>


      <div class="card-footer bg-transparent">
        <button type="submit" class="btn btn-primary">登録</button>
      </div>

    </form>
  </div>

</form>

@stop

@section('css')
@stop

@section('js')

@stop


