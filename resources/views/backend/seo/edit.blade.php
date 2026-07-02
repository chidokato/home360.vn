@extends('backend.layouts.app')

@section('title', 'SEO')
@section('page_title', 'SEO')
@section('breadcrumb', 'SEO')

@section('content')
    <form action="{{ route('backend.seo.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">SEO trang tinh</h4>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Luu SEO</button>
                    <a href="{{ route('backend.admin.dashboard') }}" class="btn btn-light">Quay lai</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach ($pages as $pageKey => $label)
                        @php
                            $config = $seoConfigs->get($pageKey);
                        @endphp
                        <div class="col-12">
                            <div class="border rounded p-3 h-100">
                                <h5 class="mb-3">{{ $label }}</h5>

                                <div class="mb-3">
                                    <label for="{{ $pageKey }}_title" class="form-label">SEO title</label>
                                    <input
                                        type="text"
                                        id="{{ $pageKey }}_title"
                                        name="{{ $pageKey }}[title]"
                                        class="form-control @error($pageKey . '.title') is-invalid @enderror"
                                        value="{{ old($pageKey . '.title', $config?->title) }}"
                                    >
                                    @error($pageKey . '.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="{{ $pageKey }}_description" class="form-label">SEO description</label>
                                    <textarea
                                        id="{{ $pageKey }}_description"
                                        name="{{ $pageKey }}[description]"
                                        rows="3"
                                        class="form-control @error($pageKey . '.description') is-invalid @enderror"
                                    >{{ old($pageKey . '.description', $config?->description) }}</textarea>
                                    @error($pageKey . '.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label for="{{ $pageKey }}_keywords" class="form-label">SEO keywords</label>
                                    <textarea
                                        id="{{ $pageKey }}_keywords"
                                        name="{{ $pageKey }}[keywords]"
                                        rows="2"
                                        class="form-control @error($pageKey . '.keywords') is-invalid @enderror"
                                        placeholder="keyword 1, keyword 2, keyword 3"
                                    >{{ old($pageKey . '.keywords', $config?->keywords) }}</textarea>
                                    @error($pageKey . '.keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
@endsection
