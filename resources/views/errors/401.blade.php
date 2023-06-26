{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('アクセス権限がありません。'))
