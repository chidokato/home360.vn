@extends('backend.layouts.app')

@section('title', 'Them can ho')
@section('page_title', 'Them can ho')
@section('breadcrumb', 'Them can ho')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Them can ho moi</h4>
            <div class="d-flex gap-2">
                <button type="submit" form="apartment-form" class="btn btn-primary">Luu can ho</button>
                <a href="{{ route('backend.apartments.index') }}" class="btn btn-light">Quay lai</a>
            </div>
        </div>
        <div class="card-body">
            <form id="apartment-form" action="{{ route('backend.apartments.store') }}" method="POST" enctype="multipart/form-data">
                @include('backend.apartments._form')
            </form>
        </div>
    </div>
@endsection
