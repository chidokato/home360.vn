@extends('backend.layouts.app')

@section('title', 'Them ' . $typeLabel)
@section('page_title', 'Them ' . $typeLabel)
@section('breadcrumb', 'Them ' . $typeLabel)

@push('styles')
    <link href="{{ asset('admin-assets/css/backend-content-form.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('admin-assets/js/backend-content-form.js') }}"></script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Them {{ strtolower($typeLabel) }} moi</h4>
            <div class="d-flex gap-2">
                <button type="submit" form="content-form" class="btn btn-primary">Luu {{ strtolower($typeLabel) }}</button>
                <a href="{{ route($type === 'product' ? 'backend.products.index' : 'backend.news.index') }}" class="btn btn-light">Quay lai</a>
            </div>
        </div>
        <div class="card-body">
            <form id="content-form" action="{{ route($type === 'product' ? 'backend.products.store' : 'backend.news.store') }}" method="POST" enctype="multipart/form-data">
                @include('backend.contents._form', ['submitLabel' => 'Luu ' . strtolower($typeLabel)])
            </form>
        </div>
    </div>
@endsection
