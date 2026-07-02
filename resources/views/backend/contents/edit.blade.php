@extends('backend.layouts.app')

@section('title', 'Sua ' . $typeLabel)
@section('page_title', 'Sua ' . $typeLabel)
@section('breadcrumb', 'Sua ' . $typeLabel)

@push('styles')
    <link href="{{ asset('admin-assets/css/backend-content-form.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('admin-assets/js/backend-content-form.js') }}"></script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Cap nhat {{ strtolower($typeLabel) }}</h4>
            <div class="d-flex gap-2">
                <button type="submit" form="content-form" name="save_stay" value="1" class="btn btn-success">Luu lai</button>
                <button type="submit" form="content-form" class="btn btn-primary">Cap nhat {{ strtolower($typeLabel) }}</button>
                <a href="{{ route($type === 'product' ? 'backend.products.index' : 'backend.news.index') }}" class="btn btn-light">Quay lai</a>
            </div>
        </div>
        <div class="card-body">
            <form id="content-form" action="{{ route($type === 'product' ? 'backend.products.update' : 'backend.news.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('backend.contents._form', ['submitLabel' => 'Cap nhat ' . strtolower($typeLabel), 'post' => $post])
            </form>
        </div>
    </div>
@endsection
