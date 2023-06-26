{{-- @extends('errors::minimal') --}}
@extends('errors.minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('セッションの有効期限が切れています'))
