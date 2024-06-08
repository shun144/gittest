@extends('adminlte::page')

@section('title','配信詳細')

@section('content_header')
    <h2>配信詳細</h2>
@stop

@section('content')

<div class="posthistoryinfo">

    <div class="back">
        <a href="{{route('owner.history')}}">
            <i class="fas fa-arrow-left"></i>
            <span>配信履歴一覧に戻る</span>
        </a>
    </div>


    <div class="card">
        <div class="card-header">
          <h3 class="card-title">配信履歴詳細表示</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <label class="form-label">配信状態</label>
                <input type="text" class="form-control bg-light" readonly value="{{isset($post) ? $post->status : 'ー'}}">
            </div>

            <div class="row">
                <label class="form-label">配信日時</label>
                <input type="text" class="form-control bg-light" readonly value="{{isset($post) ? $post->start_at : 'ー'}}" aria-describedby="startAtHelp">
                <small id="startAtHelp" class="form-text text-muted">配信日時はシステムがLINE配信処理を開始した時間です。ネットワークの影響により実際にLINE配信される時間とは異なる場合があります。</small>
            </div>
            <div class="row">
                <label class="form-label">内容</label>
                <textarea type="text" style="height:30vh; overflow-y: scroll;" class="form-control bg-light" readonly>{{isset($post) ? $post->content : 'ー'}}</textarea>
            </div>
            <div class="row">
                @if(isset($post))
                    @if($post->img_url == Null)
                        <label class="form-label">画像なし</label>
                    @else
                        <label class="form-label">画像あり</label>
                        <p class="col-12 m-0 p-0 image_preview">
                            <img src="{{$post->img_url}}" alt="画像のリンクが切れています">
                        </p>
                    @endif
                @else
                    <label class="form-label">画像なし</label>
                @endif
            </div>
            <div class="row">
                <label class="form-label">エラー</label>
                <textarea type="text" style="height:10vh; overflow-y: scroll;" class="form-control bg-light" readonly>{{isset($post) ? $post->err_info : 'ー'}}</textarea>
            </div>
        </div>
      </div>


    </div>
</div>

@stop

@section('css')
{{-- <link rel="stylesheet" href="{{ asset('build/assets/component.min.css')}}"> --}}
<link rel="stylesheet" href="{{ asset('build/assets/posthistoryinfo.css')}}">
@stop

@section('js')
@stop