{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('サーバ負荷が高まっています。時間をおいてからリクエストしてください。'))
