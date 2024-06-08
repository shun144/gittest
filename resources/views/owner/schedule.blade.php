@extends('adminlte::page')

@section('title', 'スケジュール配信')

@section('content_header')
  <div class="d-flex">
    <h1>配信</h1>
    <button class="btn btn-success ml-5" data-toggle="modal" data-target="#post_message">
      即時配信
    </button>
  </div>
@stop

@section('content')

<div class="row">
  <div class="col-md-12">
  </div>
  <div class="col-md-12">

    <div class="card">
      <div class="card-body p-0">
        <div class="card card-primary">
          <div id="calendarToken" style="display:none">{{ csrf_token()}}</div>
          <div id="calendar"></div>
        </div>
      </div>
    </div>

    
  </div>
</div>
    
  @include('owner.modals.post_message')
  @include('owner.modals.add_template')
  @include('owner.modals.edit_template')

  @include('owner.modals.add_schedule')
  @include('owner.modals.edit_schedule')
@stop


@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
  <link rel="stylesheet" href="{{ asset('build/assets/component.min.css')}}">
  <link rel="stylesheet" href="{{ asset('build/assets/schedule.css')}}">
  {{-- @vite(['resources/sass/component.scss']) --}}
@stop

@section('js')
  <script src="{{ asset('vendor/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/fullcalendar/main.min.js') }}"></script>
  {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script> --}}
  <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>

  <script src="{{ asset('build/assets/schedule.js')}}"></script>
  {{-- <script src="{{ asset('build/assets/schedule.min.js')}}"></script> --}}
  {{-- @vite(['resources/js/schedule.js']) --}}
  {{-- @vite(['resources/js/component.js']) --}}
  
  <script>

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 定型メッセージ関連ポップアップ
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    @if (isset($get_template_error_flushMsg))
    $(function () {toastr.error('{{ $get_template_error_flushMsg }}');});
    @endif

    
    @if (session('add_template_success_flushMsg'))
    $(function () {toastr.success('{{ session('add_template_success_flushMsg') }}');});
    @endif
    @if (session('add_template_error_flushMsg'))
    $(function () {toastr.error('{{ session('add_template_error_flushMsg') }}');});
    @endif

    @if (session('edit_template_success_flushMsg'))
    $(function () {toastr.success('{{ session('edit_template_success_flushMsg') }}');});
    @endif
    @if (session('edit_template_error_flushMsg'))
    $(function () {toastr.error('{{ session('edit_template_error_flushMsg') }}');});
    @endif

    @if (session('del_template_success_flushMsg'))
    $(function () {toastr.success('{{ session('del_template_success_flushMsg') }}');});
    @endif
    @if (session('del_template_error_flushMsg'))
    $(function () {toastr.error('{{ session('del_template_error_flushMsg') }}');});
    @endif

    
  </script>
@stop