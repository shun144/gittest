{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('サーバ負荷が高まっています。時間をおいてからリクエストしてください。'))
