@extends('adminlte::page')

@section('title','あいさつメッセージ')


@section('content_header')
    <h1>あいさつメッセージ</h1>
@stop

@section('content')
<div class="greeting">

    <div class="card mx-auto w-75">
        <div class="card-header">
            <button form="form_greet_save" type="submit" class="btn btn-primary">更新</button>
        </div>
        <div class="card-body">
            <form id="form_greet_save" action="{{ route('post') }}" method="post" enctype="multipart/form-data" onSubmit="greetSave(event)">
                <input id="greetSaveCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="row greetFormTextAreaRow mb-5">
                    <label for="message_content" class="form-label">配信内容</label>
                    <textarea class="form-control content_form" name="content" rows="10" maxlength="1000"  aria-describedby="contentHelp">{{isset($post) ? $post->content : ''}}</textarea>
                    <div class="invalid-feedback content_feedback"></div> 
                    <small id="contentHelp" class="form-text text-muted">LINEで通知される内容です（上限文字数：1000）</small>
                </div>


                <div class="row mb-2">
                    <label for="input_file">配信画像</label>
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
                            <input type="hidden" name='has_file' class='has_file' value={{isset($post) ? $post->has_file : '0'}}>
                          </label>
                        </div>
                        <input type="text" class="filename_view form-control" readonly="" value={{isset($post) ? $post->org_name : ''}}>
                    </div> 
                    <small id="imgFileHelp" class="mt-0 mb-2 form-text text-muted">送信可能な拡張子はpng,jpegです（上限ファイル数：1）</small>
                      
                    @if(isset($post) and $post->img_url != Null)
                    <p class="col-12 m-0 p-0 image_preview">
                        <img src="{{$post->img_url}}" alt="画像のリンクが切れています">
                    </p>
                    @else
                        <p class="col-12 m-0 p-0 image_preview"></p>
                    @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/component.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
<script src="{{ asset('build/assets/component.js')}}"></script>

<script>
const URL_ROOT = $(location).attr('origin');
const URL_STORAGE = URL_ROOT + '/storage/owner/image/greeting';
const URL_DASHBOARD = URL_ROOT + '/dashboard';
const URL_MEDSSAGE_POST = URL_DASHBOARD + '/greeting-link-edit';

function greetSave(e){
  e.preventDefault();
  const msg = 'あいさつメッセージを更新してよろしいですか?'
  if(!window.confirm(msg))
  {
    return false;
  }
  const csrf_token = document.getElementById('greetSaveCsrfToken').value;
  let $form = $('#form_greet_save');
  let fd = new FormData($form.get(0));
  $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
    url: URL_MEDSSAGE_POST,
    method: 'POST',
    contentType: false,
    processData: false,
    data: fd
  }).done(function(res){
    toastr.info('あいさつメッセージを更新しました');
  }).fail(function(res){
    toastr.error('あいさつメッセージ更新に失敗しました');
    console.log(res);
  })
};
</script>

@stop
