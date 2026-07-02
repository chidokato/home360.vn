@extends('backend.layouts.app')

@section('title', 'Sua can ho')
@section('page_title', 'Sua can ho')
@section('breadcrumb', 'Sua can ho')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Cap nhat can ho</h4>
            <div class="d-flex gap-2">
                <button type="submit" form="apartment-form" class="btn btn-primary">Luu can ho</button>
                <a href="{{ route('backend.apartments.index', ['project_id' => $apartment->project_id]) }}" class="btn btn-light">Quay lai</a>
            </div>
        </div>
        <div class="card-body">
            <form id="apartment-form" action="{{ route('backend.apartments.update', $apartment) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('backend.apartments._form')
            </form>
        </div>
    </div>
@endsection
