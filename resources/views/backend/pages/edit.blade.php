@extends('backend.layouts.app')

@section('title', 'Sua page')
@section('page_title', 'Sua page')
@section('breadcrumb', 'Sua page')

@section('content')
    <form action="{{ route('backend.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('backend.pages._form')

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('backend.pages.index') }}" class="btn btn-light">Huy</a>
            <button type="submit" name="save_stay" value="1" class="btn btn-soft-primary">Luu va tiep tuc sua</button>
            <button type="submit" class="btn btn-primary">Luu</button>
        </div>
    </form>
@endsection
