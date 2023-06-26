{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('サーバエラーが発生しています。管理者へ問い合わせをしてください。'))
