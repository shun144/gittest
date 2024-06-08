@extends('adminlte::page')

@section('title', '配信')

@section('content_header')
  <h2>配信</h2>
@stop


@section('content')

  <div class="delivery">
    <form id="form_post_message" action="{{ route('post') }}" method="post" enctype="multipart/form-data" onSubmit="postImmediately(event)">
      <input id="postImiCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
      <div class="card">
        <div class="card-header">
          <button type="submit" class="btn btnPostImi">送 信</button>
        </div>
  
        <div class="card-body">
          <div class="form-group">
            <label for="content" class="form-label">配信内容</label>
            <small id="contentHelp" class="form-text text-muted">LINEで通知される内容です（上限文字数: 1,000）</small>
            <textarea class="form-control content_form" name="content" rows="10" maxlength="1000"  aria-describedby="contentHelp"></textarea>
            <div class="invalid-feedback content_feedback"></div> 
          </div>
    
          <div class="form-group">
            <label for="input_file">配信画像</label>
            <small id="imgFileHelp" class="mt-0 mb-2 form-text text-muted">送信可能な拡張子はpng,jpegです（上限ファイル数: 1）</small>
            <div class="image_component" style="width:100%">
              <div class="input-group">
                <div class="btn-group">
                  <label>
                    <span class="btn btn-outline-primary">選択
                      <input type="file" style="display:none;" multiple="multiple" class="form-control-file image_form" name="imagefile[]" accept="image/jpeg,image/png" aria-describedby="imgFileHelp"/>
                    </span>
                  </label>
                  <label>
                    <span class="btn btn-outline-danger btn_del_file">削除</span>
                    <input type="hidden" name='has_file' class='has_file'>
                  </label>
                </div>
                <input type="text" class="filename_view form-control" readonly="">
              </div>             
              <p class="image_preview"></p>
            </div>
          </div>
        </div>
  
      </div>
    </form>
  </div>
</div>  
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('build/assets/delivery.css')}}">
@stop

@section('js')
<script src="{{ asset('build/assets/delivery.js')}}" defer></script>
@stop
