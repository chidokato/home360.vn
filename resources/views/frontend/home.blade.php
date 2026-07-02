@extends('frontend.layouts.app')

@section('title', $pageTitle ?? '')
@section('meta_description', $pageDescription ?? '')
@section('meta_keywords', $pageKeywords ?? '')

@section('content')
    @include('frontend.partials.home-body')
@endsection
