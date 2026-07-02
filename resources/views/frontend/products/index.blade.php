@extends('frontend.layouts.app')

@section('title', $pageTitle ?? $category->name)
@section('meta_description', $pageDescription ?? ($category->description ?: 'Danh sách sản phẩm'))

@push('styles')
    <style>
        .product-empty-state {
            padding: 48px 32px;
            border-radius: 24px;
            border: 1px dashed rgba(20, 24, 32, 0.16);
            background: rgba(246, 247, 249, 1);
            text-align: center;
        }

        .product-sort-form {
            min-width: 240px;
        }

        .product-list-grid .wishlist {
            top: 16px;
            right: 16px;
        }

        @media (max-width: 991.98px) {
            .product-sort-form {
                min-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $formatNumber = static fn ($value) => filled($value) ? number_format((float) $value, 0, ',', '.') : null;
        $formatDecimal = static fn ($value) => filled($value) ? rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') : null;
        $formatPrice = static function ($value) use ($formatDecimal, $formatNumber) {
            if (! filled($value)) {
                return null;
            }

            $amount = (float) $value;

            if ($amount >= 1000000000) {
                return str_replace('.', ',', $formatDecimal($amount / 1000000000)) . ' tỷ';
            }

            if ($amount >= 1000000) {
                return str_replace('.', ',', $formatDecimal($amount / 1000000)) . ' triệu';
            }

            return $formatNumber($amount) . ' VND';
        };
        $formatRange = static function ($from, $to, $suffix = '') use ($formatDecimal) {
            $fromValue = $formatDecimal($from);
            $toValue = $formatDecimal($to);

            if ($fromValue && $toValue) {
                return $fromValue . ' - ' . $toValue . $suffix;
            }

            if ($fromValue) {
                return 'Tu ' . $fromValue . $suffix;
            }

            if ($toValue) {
                return 'Den ' . $toValue . $suffix;
            }

            return null;
        };

        $productImage = static fn ($product) => $product->image ? asset(ltrim($product->image, '/')) : asset('images/section/properties-details-12.jpg');
        $productPrice = static fn ($product) => $formatPrice($product->price) ?: 'Liên hệ';
        $productArea = static fn ($product) => $product->area ? $formatDecimal($product->area) . ' m2' : $formatRange($product->area_from, $product->area_to, ' m2');
        $productBedrooms = static fn ($product) => filled($product->bedroom_count) ? $product->bedroom_count . ' PN' : $formatRange($product->bedroom_count_from, $product->bedroom_count_to, ' PN');
        $productBathrooms = static fn ($product) => filled($product->bathroom_count) ? $product->bathroom_count . ' PT' : $formatRange($product->bathroom_count_from, $product->bathroom_count_to, ' PT');
        $currentSort = in_array(($currentSort ?? 'default'), ['default', 'new', 'old'], true) ? $currentSort : 'default';
    @endphp

    <div class="main-content">
        <div class="section-properties">
            <div class="tf-spacing-1">
                <div class="tf-container">
                    <div class="box-title mb_40">
                        <div>
                            <ul class="breadcrumb style-1 text-button fw-4 mb_4">
                                <li><a href="{{ route('frontend.home') }}">Home</a></li>
                                <li>{{ $category->name }}</li>
                            </ul>
                            <h4>{{ $category->name }}</h4>
                        </div>
                        <div class="right d-flex gap_12">
                            <ul class="nav-tab-filter align-items-center group-layout d-flex gap_12" role="tablist">
                                <li class="nav-tab-item" role="presentation">
                                    <a href="#gridLayout" class="btn-layout grid nav-link-item active" data-bs-toggle="tab">
                                        <i class="icon-SquaresFour"></i>
                                    </a>
                                </li>
                                <li class="nav-tab-item" role="presentation">
                                    <a href="#listLayout" class="nav-link-item btn-layout list" data-bs-toggle="tab">
                                        <i class="icon-Rows"></i>
                                    </a>
                                </li>
                            </ul>
                            <form method="GET" action="{{ url()->current() }}" class="product-sort-form">
                                <select name="sort" class="select_js select-sort style-2" onchange="this.form.submit()">
                                    <option value="default" @selected($currentSort === 'default')>Sort by (Default)</option>
                                    <option value="new" @selected($currentSort === 'new')>Newest</option>
                                    <option value="old" @selected($currentSort === 'old')>Oldest</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="flat-animate-tab">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="gridLayout" role="tabpanel">
                                @if ($products->isEmpty())
                                    <div class="product-empty-state">
                                        <h5 class="mb_12">Chưa có dự án trong danh mục này</h5>
                                        <p class="text-body-default text_secondary-color mb-0">Nội dung se duoc cap nhat trong thoi gian toi.</p>
                                    </div>
                                @else
                                    <div class="tf-grid-layout lg-col-3 md-col-2 product-list-grid">
                                        @foreach ($products as $product)
                                            @php
                                                $image = $productImage($product);
                                                $price = $productPrice($product);
                                                $area = $productArea($product);
                                                $bedrooms = $productBedrooms($product);
                                                $bathrooms = $productBathrooms($product);
                                            @endphp
                                            <div class="card-house style-default hover-image" data-id="{{ $product->id }}">
                                                <div class="img-style mb_20">
                                                    <img loading="lazy" decoding="async" src="{{ $image }}" alt="{{ $product->title }}">
                                                    <div class="wrap-tag d-flex gap_8 mb_12">
                                                        <div class="tag sale text-button-small fw-6 text_primary-color">Dự án</div>
                                                        @if ($product->category)
                                                            <div class="tag categoreis text-button-small fw-6 text_primary-color">{{ $product->category->name }}</div>
                                                        @endif
                                                    </div>
                                                    <a href="{{ $product->frontend_url }}" class="overlay-link"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="price mb_12">
                                                        {{ $price }}
                                                    </h4>
                                                    <a href="{{ $product->frontend_url }}" class="title mb_8 h5 link text_primary-color">{{ $product->title }}</a>
                                                    <p>{{ $product->address ?: '...' }}</p>
                                                    <ul class="info d-flex">
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Bed"></i>{{ $bedrooms ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Bathstub"></i>{{ $bathrooms ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Ruler"></i>{{ $area ?: '...' }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane" id="listLayout" role="tabpanel">
                                @if ($products->isEmpty())
                                    <div class="product-empty-state">
                                        <h5 class="mb_12">Chưa có dự án trong danh mục này</h5>
                                        <p class="text-body-default text_secondary-color mb-0">Nội dung se duoc cap nhat trong thoi gian toi.</p>
                                    </div>
                                @else
                                    <div class="wrap-list d-grid gap_30">
                                        @foreach ($products as $product)
                                            @php
                                                $image = $productImage($product);
                                                $price = $productPrice($product);
                                                $area = $productArea($product);
                                                $bedrooms = $productBedrooms($product);
                                                $bathrooms = $productBathrooms($product);
                                            @endphp
                                            <div class="card-house style-list v2" data-id="{{ $product->id }}">
                                                <div class="wrap-img">
                                                    <a href="{{ $product->frontend_url }}" class="img-style">
                                                        <img loading="lazy" decoding="async" src="{{ $image }}" alt="{{ $product->title }}">
                                                    </a>
                                                    <a href="{{ $product->frontend_url }}" class="img-style">
                                                        <img loading="lazy" decoding="async" src="{{ $image }}" alt="{{ $product->title }}">
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <div class="d-flex align-items-center gap_6 top mb_16 flex-wrap justify-content-between">
                                                        <h4 class="price">
                                                            {{ $price }}
                                                        </h4>
                                                        <div class="wrap-tag d-flex gap_8">
                                                            <div class="tag sale text-button-small fw-6 text_primary-color">Dự án</div>
                                                            @if ($product->category)
                                                                <div class="tag categoreis text-button-small fw-6 text_primary-color">{{ $product->category->name }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <a href="{{ $product->frontend_url }}" class="title mb_8 h5 link text_primary-color">{{ $product->title }}</a>
                                                    <p>{{ $product->address ?: '...' }}</p>
                                                    <ul class="info d-flex">
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Bed"></i>{{ $bedrooms ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Bathstub"></i>{{ $bathrooms ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                            <i class="icon-Ruler"></i>{{ $area ?: '...' }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
