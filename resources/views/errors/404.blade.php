{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('お探しのページは見つかりませんでした。'))
{{-- @section('message', __('Not Found')) --}}
