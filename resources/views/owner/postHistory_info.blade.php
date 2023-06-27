@extends('adminlte::page')

@section('title','Dashboard')

@section('content_header')
    <h1>配信詳細</h1>
@stop

@section('content')

<div class="mx-auto pb-5" style="width:70rem">
    <div class="text-left">
        <a href="{{route('owner.history')}}" class="h5">
            <i class="fas fa-arrow-left"></i>
            <span>配信履歴一覧に戻る</span>
        </a>
    </div>
    <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">配信履歴詳細表示</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="form-label">状態</label>
                {{-- <input type="text" class="form-control bg-light" readonly value="配信待"> --}}
                <input type="text" class="form-control bg-light" readonly value="{{$posts->status}}">
            </div>
            <div class="row mb-3">
                <label class="form-label">配信日時</label>
                <input type="text" class="form-control bg-light" readonly value="{{$posts->start_at}}" aria-describedby="startAtHelp">
                <small id="startAtHelp" class="form-text text-muted">配信日時はシステムがLINE配信処理を開始した時間です。ネットワークの影響により実際にLINE配信される時間とは異なる場合があります。</small>
            </div>
            {{-- <div class="row mb-3">
                <label class="form-label">終了日時</label>
                <input type="text" class="form-control bg-light" readonly value="{{$posts->end_at}}">
            </div> --}}

            <div class="row mb-3">
                <label class="form-label">タイトル</label>
                <input type="text" class="form-control bg-light" readonly value="{{$posts->title}}">
            </div>
            <div class="row mb-3">
                <label class="form-label">内容</label>
                <textarea type="text" style="height:30vh; overflow-y: scroll;" class="form-control bg-light" readonly>{{$posts->content}}</textarea>
            </div>
            <div class="row mb-3">
                @if($posts->img_url == Null)
                    <label class="form-label">画像なし</label>
                @else
                    <label class="form-label">画像あり</label>
                    <p class="col-12 m-0 p-0 image_preview">
                        <img src="{{$posts->img_url}}" alt="画像のリンクが切れています">
                    </p>
                @endif
            </div>
            <div class="row mb-3">
                <label class="form-label">エラー</label>
                <textarea type="text" style="height:10vh; overflow-y: scroll;" class="form-control bg-light" readonly>{{$posts->err_info}}</textarea>
            </div>
        </div>
      </div>
      {{-- <div class="text-right">
        <a href="{{route('owner.history')}}" class="h5">
            <i class="fas fa-arrow-left"></i>
            <span>配信履歴一覧に戻る</span>
        </a>
      </div> --}}
</div>



@stop

@section('css')
@vite(['resources/sass/component.scss'])
@stop

@section('js')
@vite(['resources/js/component.js'])
@stop