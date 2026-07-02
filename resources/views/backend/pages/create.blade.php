@extends('backend.layouts.app')

@section('title', 'Them page')
@section('page_title', 'Them page')
@section('breadcrumb', 'Them page')

@section('content')
    <form action="{{ route('backend.pages.store') }}" method="POST" enctype="multipart/form-data">
        @include('backend.pages._form')

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('backend.pages.index') }}" class="btn btn-light">Huy</a>
            <button type="submit" class="btn btn-primary">Luu</button>
        </div>
    </form>
@endsection
