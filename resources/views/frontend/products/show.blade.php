
@extends('frontend.layouts.app')

@section('title', $pageTitle ?? $product->title)
@section('meta_description', $pageDescription ?? ($product->summary ?: 'Chi tiết dự án'))

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/magnific-popup.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/product-show.css') }}">
    <style>
        .project-related-card .content .wrap-tag {
            position: static;
            top: auto;
            left: auto;
            margin-bottom: 12px;
        }
    </style>
@endpush

@section('content')
    @php
        $gallery = collect([$product->image])
            ->merge(
                $product->galleryImages
                    ->filter(fn ($image) => ($image->image_type ?? \App\Models\PostImage::TYPE_PERSPECTIVE) === \App\Models\PostImage::TYPE_PERSPECTIVE)
                    ->pluck('image')
            )
            ->filter()
            ->unique()
            ->take(5)
            ->values();

        if ($gallery->isEmpty()) {
            $gallery = collect(['images/section/properties-details-11.jpg']);
        }

        $interiorGallery = $product->galleryImages
            ->filter(fn ($image) => ($image->image_type ?? \App\Models\PostImage::TYPE_PERSPECTIVE) === \App\Models\PostImage::TYPE_INTERIOR)
            ->pluck('image')
            ->filter()
            ->unique()
            ->values();

        $amenityGallery = $product->galleryImages
            ->filter(fn ($image) => ($image->image_type ?? \App\Models\PostImage::TYPE_PERSPECTIVE) === \App\Models\PostImage::TYPE_AMENITY)
            ->pluck('image')
            ->filter()
            ->unique()
            ->values();

        $buildGalleryGrid = static function ($images) {
            $images = collect($images)->values();

            if ($images->isEmpty()) {
                return [
                    'featured' => null,
                    'rows' => collect(),
                    'hidden' => collect(),
                ];
            }

            return [
                'featured' => $images->first(),
                'rows' => $images->slice(1, 4)->chunk(2),
                'hidden' => $images->slice(5)->values(),
            ];
        };

        $amenityGalleryGrid = $buildGalleryGrid($amenityGallery);
        $interiorGalleryGrid = $buildGalleryGrid($interiorGallery);
        $floorPlans = collect($product->floorPlans ?? [])->filter(fn ($plan) => filled($plan->image))->values();
        $apartments = collect($product->apartments ?? [])->filter(fn ($apartment) => (bool) ($apartment->is_active ?? false))->values();
        $locationImage = filled($product->location_image ?? null) ? $product->location_image : null;

        $displayImage = static fn ($path) => asset(ltrim($path, '/'));
        $displayText = static fn ($value, $fallback = '...') => filled($value) ? $value : $fallback;
        $defaultSeller = (object) [
            'name' => '',
            'job_title' => '',
            'bio' => '',
            'address' => '',
            'phone' => '',
            'secondary_phone' => '',
            'whatsapp_phone' => '',
            'avatar' => '',
        ];
        $seller = $contactSeller ?? $defaultSeller;
        $sellerName = trim((string) ($seller->name ?? ''));
        $sellerAddress = trim((string) ($seller->address ?? ''));
        $sellerPhone = trim((string) ($seller->phone ?? ''));
        $sellerSecondaryPhone = trim((string) ($seller->secondary_phone ?? '')) ?: null;
        $sellerAvatar = trim((string) ($seller->avatar ?? ''));
        $sellerEmail = trim((string) ($seller->email ?? ''));
        $callPhone = preg_replace('/\D+/', '', $sellerPhone);
        $zaloPhone = preg_replace('/\D+/', '', trim((string) ($seller->whatsapp_phone ?? $sellerPhone)));
        $directionsUrl = filled($sellerAddress) ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($sellerAddress) : '#';
        $zaloUrl = $zaloPhone !== '' ? 'https://zalo.me/' . $zaloPhone : '#';
        $priceListUrl = collect([
            data_get($product, 'price_list_url'),
            data_get($product, 'brochure_url'),
            data_get($product, 'download_url'),
            data_get($product, 'attachment'),
            data_get($product, 'file'),
        ])->filter(fn ($value) => filled($value))->map(fn ($value) => trim((string) $value))->first() ?? '';
        $priceListHref = filled($priceListUrl) ? ((str_starts_with($priceListUrl, 'http://') || str_starts_with($priceListUrl, 'https://')) ? $priceListUrl : $displayImage($priceListUrl)) : '#';
        $normalizedAddressParts = collect([
            trim((string) ($product->address ?? '')),
            trim((string) ($product->ward->name ?? '')),
            trim((string) (($product->province->name ?? optional($product->ward->province)->name) ?? '')),
        ])->filter(fn ($value) => $value !== '')
            ->reduce(function ($carry, $value) {
                $normalized = mb_strtolower($value);

                if (! collect($carry)->contains(fn ($item) => mb_strtolower($item) === $normalized)) {
                    $carry[] = $value;
                }

                return $carry;
            }, []);
        $fullAddressText = ! empty($normalizedAddressParts)
            ? implode(', ', $normalizedAddressParts)
            : $displayText($product->address);
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
                return 'Từ ' . $fromValue . $suffix;
            }

            if ($toValue) {
                return 'Đến ' . $toValue . $suffix;
            }

            return null;
        };

        $areaText = $product->area ? $formatDecimal($product->area) . ' m2' : $formatRange($product->area_from, $product->area_to, ' m2');
        $floorText = filled($product->floor_count) ? $product->floor_count . ' tầng' : $formatRange($product->floor_count_from, $product->floor_count_to, ' tầng');
        $unitText = filled($product->unit_count) ? $product->unit_count . ' căn' : $formatRange($product->unit_count_from, $product->unit_count_to, ' căn');
        $bedroomText = filled($product->bedroom_count) ? $product->bedroom_count . ' ngủ' : $formatRange($product->bedroom_count_from, $product->bedroom_count_to, ' ngủ');
        $bathroomText = filled($product->bathroom_count) ? $product->bathroom_count . ' wc' : $formatRange($product->bathroom_count_from, $product->bathroom_count_to, ' wc');
        $priceText = $formatPrice($product->price) ?: 'Liên hệ';
        $overviewItems = [
            ['label' => 'Loại hình', 'value' => $displayText(optional($product->category)->name)],
            ['label' => 'Diện tích', 'value' => $displayText($areaText)],
            ['label' => 'Phòng ngủ', 'value' => $displayText($bedroomText)],
            ['label' => 'WC', 'value' => $displayText($bathroomText)],
            ['label' => 'Số tầng', 'value' => $displayText($floorText)],
            ['label' => 'Số căn', 'value' => $displayText($unitText)],
        ];
    @endphp

    <div class="main-content section-onepage">
        <div class="properties-details tf-spacing-1 pt-0">
            <div class="properties-hero">
                <div class="properties-title">
                    <div>
                        <ul class="breadcrumb style-1 text-button fw-4 mb_16">
                            <li><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                            @if ($product->category)
                                <li>
                                    <a href="{{ route('frontend.categories.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                                </li>
                            @endif
                            <li>{{ $product->title }}</li>
                        </ul>

                        <h2 class="mt-4">{{ $product->title }}</h2>
                        <ul class="list-action d-flex gap_16 flex-wrap">
                            <li>
                                <span class="gap_8 d-inline-flex align-items-center">
                                    <i class="icon-MapPin"></i>
                                    <span class="text-button">{{ $fullAddressText }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="section mt-5">
                        <div class="properties-overview v3">
                            <div class="tf-grid-layout tf-col-2 lg-col-6">
                                @foreach ($overviewItems as $item)
                                    <div class="item d-flex gap_16">
                                        <i class="icon icon-SlidersHorizontal"></i>
                                        <div class="d-flex flex-column gap">
                                            <span class="text-body-default">{{ $item['label'] }}:</span>
                                            <span class="text-title fw-6 text_primary-color">{{ $item['value'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="mb_16">Giá bán:</h5>
                        <h2 class="price">{{ $priceText }}</h2>
                        @if ($product->published_at)
                            <p class="text-body-default text_secondary-color mt_12">
                                Cập nhật {{ $product->published_at->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="right">
                    <div class="wrap-thumb">
                        <div class="swiper sw-single">
                            <div class="swiper-wrapper">
                                @foreach ($gallery as $image)
                                    <div class="swiper-slide">
                                        <div class="thumb-main project-gallery-main">
                                            <a href="{{ $displayImage($image) }}" data-fancybox="project-gallery" class="img-style">
                                                <img src="{{ $displayImage($image) }}" alt="{{ $product->title }}">
                                            </a>
                                            <div class="wrap-btn d-flex gap_10 flex-wrap">
                                                <button type="button" class="tf-btn btn-bg-1 border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                                                    <span class="d-flex align-items-center gap_8">
                                                        <i class="icon-PhoneCall"></i>Nhận tư vấn
                                                    </span>
                                                    <span class="bg-effect"></span>
                                                </button>
                                                <a href="{{ $displayImage($image) }}" data-fancybox="project-gallery" class="tf-btn btn-bg-1">
                                                    <span class="d-flex align-items-center gap_8">
                                                        <i class="icon-Image"></i>Xem album
                                                    </span>
                                                    <span class="bg-effect"></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if ($gallery->count() > 1)
                        <div class="sw-button sw-thumbs-prev">
                            <i class="icon-CaretLeft"></i>
                        </div>
                        <div class="sw-button sw-thumbs-next">
                            <i class="icon-CaretRight"></i>
                        </div>
                    @endif
                    <div class="wrap-pagi">
                        <div class="swiper thumbs-sw-pagi" data-preview="6" data-mobile="3" data-space="12">
                            <div class="swiper-wrapper">
                                @foreach ($gallery as $image)
                                    <div class="swiper-slide">
                                        <div class="image-detail project-gallery-thumb">
                                            <img src="{{ $displayImage($image) }}" alt="{{ $product->title }} thumbnail">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="properties-menut-list">
                <div class="tf-container">
                    <ul class="tab-slide overflow-x-auto" id="navbar">
                        <li class="text-button nav-tab-item text_primary-color active"><a href="#chinh-sach-ban-hang" class="nav_link">CS bán hàng</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#vi-tri" class="nav_link">Vị trí</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#tong-quan" class="nav_link">Tổng quan</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#tien-ich" class="nav_link">Tiện ích</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#noi-that" class="nav_link">Nội thất</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#mat-bang" class="nav_link">Mặt bằng</a></li>
                        <li class="text-button nav-tab-item text_primary-color"><a href="#can-ho" class="nav_link">Căn hộ</a></li>
                    </ul>
                </div>
            </div>

            <div class="tf-container">
                <div class="row">
                                        <div class="col-lg-8">
                        <div id="chinh-sach-ban-hang" class="section tf-spacing-9 mb-5">
                            <div class="project-detail-card">
                                <!-- <h5 class="properties-title mb_20">CS bán hàng</h5> -->
                                @if (filled(trim(strip_tags((string) ($product->sales_policy ?? '')))))
                                    <div class="project-detail-content">
                                        {!! $product->sales_policy !!}
                                    </div>
                                @else
                                    <ul class="project-detail-list mb-0">
                                        <li><span>Giá bán</span><strong>{{ $priceText }}</strong></li>
                                        <li><span>Địa chỉ</span><strong>{{ $fullAddressText }}</strong></li>
                                        <li><span>Loại hình</span><strong>{{ $displayText(optional($product->category)->name) }}</strong></li>
                                        <li><span>Liên hệ tư vấn</span><strong>{{ $displayText($seller->phone ?? $defaultSeller->phone) }}</strong></li>
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div id="vi-tri" class="section tf-spacing-9 pt-0 mb-5">
                            <div class="project-detail-card">
                                <h5 class="properties-title mb_20">Vị trí dự án</h5>
                                <p class="text-body-default text_secondary-color mb_12">
                                    {{ $fullAddressText }}
                                </p>
                                @if ($locationImage)
                                    <div class="project-location-image">
                                        <img src="{{ $displayImage($locationImage) }}" alt="{{ $product->title }} location">
                                    </div>
                                @endif
                                @if (filled(trim((string) $product->map_embed)))
                                    <div class="project-map-embed">
                                        {!! $product->map_embed !!}
                                    </div>
                                @else
                                    <!-- <div class="project-summary">
                                        <p class="text-body-default text_secondary-color mb-0">{{ $displayText($product->address) }}</p>
                                    </div> -->
                                @endif
                            </div>
                        </div>

                        <div id="tong-quan" class="section tf-spacing-9 pt-0 mb-5">
                            @if (filled(trim(strip_tags((string) $product->summary))))
                                <div class="project-summary mb_24">
                                    <h5 class="properties-title mb_12">{{ $product->title }}</h5>
                                    <div class="project-detail-content text-body-default text_secondary-color mb-0">
                                        {!! $product->summary !!}
                                    </div>
                                </div>
                            @endif

                            <div class="project-detail-card mt_24">
                                <h5 class="properties-title mb_20">Mô tả dự án</h5>
                                <div class="project-detail-content">
                                    {!! $product->content ?: '<p>Thông tin mô tả đang được cập nhật.</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="project-sticky-box tf-spacing-9">
                            <div class="box-sellers project-contact-box">
                                <h5 class="project-contact-box-title">Liên hệ tư vấn</h5>
                                <div class="project-contact-author">
                                    @if (filled($sellerAvatar))
                                        <div class="avatar">
                                            <img src="{{ $displayImage($sellerAvatar) }}" width="354" height="354" alt="{{ $sellerName }}">
                                        </div>
                                    @endif
                                    <div class="author-info">
                                        @if (filled($sellerName))
                                            <h6>{{ $sellerName }}</h6>
                                        @endif
                                        @if (filled($sellerPhone))
                                            <p>{{ $sellerPhone }}</p>
                                        @endif
                                    </div>
                                </div>
                                <ul class="project-contact-list">
                                    @if (filled($sellerAddress))
                                        <li class="project-contact-item">
                                            <span class="project-contact-icon">
                                                <i class="icon-MapPin"></i>
                                            </span>
                                            <div class="project-contact-copy">
                                                <p>{{ $sellerAddress }}</p>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                                <div class="project-contact-actions">
                                        @if ($callPhone !== '')
                                            <a href="{{ 'tel:' . $callPhone }}" class="tf-btn btn-bg-1 w-full">
                                                <span class="d-flex align-items-center gap_8"><i class="icon-PhoneCall"></i>Gọi hotline</span>
                                                <span class="bg-effect"></span>
                                            </a>
                                        @endif
                                        @if ($zaloPhone !== '')
                                            <a href="{{ $zaloUrl }}" class="tf-btn btn-bg-primary-2 w-full" target="_blank" rel="noopener">
                                                <span class="d-flex align-items-center gap_8"><i class="icon-ChatCircleDots"></i>Chat Zalo</span>
                                                <span class="bg-effect"></span>
                                            </a>
                                        @endif
                                        @if (filled($priceListUrl))
                                            <a href="{{ $priceListHref }}" class="tf-btn btn-bg-primary-2 w-full" target="_blank" rel="noopener" download>
                                                <span class="d-flex align-items-center gap_8"><i class="icon-DownloadSimple"></i>Tải bảng giá dự án</span>
                                                <span class="bg-effect"></span>
                                            </a>
                                        @else
                                            <button type="button" class="tf-btn btn-bg-primary-2 w-full border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                                                <span class="d-flex align-items-center gap_8"><i class="icon-DownloadSimple"></i>Tải bảng giá dự án</span>
                                                <span class="bg-effect"></span>
                                            </button>
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-5">
                        <div id="tien-ich" class="section tf-spacing-9 pt-0 mb-5">
                            <div class="project-detail-card">
                                <h5 class="properties-title mb_20">Tiện ích</h5>
                                @if ($amenityGallery->isNotEmpty())
                                    <div class="properties-gallery project-gallery-grid">
                                        <div class="tf-grid-layout md-col-2 gap_20">
                                            <div class="img-style position-relative project-gallery-featured">
                                                <a href="{{ $displayImage($amenityGalleryGrid['featured']) }}" data-fancybox="project-amenity-gallery">
                                                    <img src="{{ $displayImage($amenityGalleryGrid['featured']) }}" alt="{{ $product->title }} amenity">
                                                </a>
                                            </div>
                                            <div class="project-gallery-secondary">
                                                @foreach ($amenityGalleryGrid['rows'] as $galleryRow)
                                                    <div class="wrap-img d-flex gap_20 {{ $loop->last ? '' : 'mb_20' }}">
                                                        @foreach ($galleryRow as $image)
                                                            <a href="{{ $displayImage($image) }}" data-fancybox="project-amenity-gallery" class="project-gallery-secondary-item">
                                                                <img src="{{ $displayImage($image) }}" alt="{{ $product->title }} amenity">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @foreach ($amenityGalleryGrid['hidden'] as $image)
                                            <a href="{{ $displayImage($image) }}" data-fancybox="project-amenity-gallery" class="d-none">
                                                <img src="{{ $displayImage($image) }}" alt="{{ $product->title }} amenity">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mb-0 text_secondary-color">Nội dung tiện ích đang được cập nhật.</p>
                                @endif
                            </div>
                        </div>
                        <div id="mat-bang" class="section tf-spacing-9 pt-0 mb-5">
                            <div class="project-detail-card">
                                <h5 class="properties-title mb_20">Mặt bằng</h5>
                                @if ($floorPlans->isNotEmpty())
                                    <div class="project-floor-plan-grid">
                                        @foreach ($floorPlans as $floorPlan)
                                            <a href="{{ $displayImage($floorPlan->image) }}" data-fancybox="project-floor-plans" class="project-floor-plan-card">
                                                <img src="{{ $displayImage($floorPlan->image) }}" alt="{{ $floorPlan->name ?: $product->title }}" style="width: 100%; height: auto !important; aspect-ratio: 1/1; object-fit: cover; background-color: #fff;">
                                                <div class="project-floor-plan-body">
                                                    <strong>{{ $floorPlan->name ?: 'Mặt bằng dự án' }}</strong>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mb-0 text_secondary-color">Nội dung mặt bằng đang được cập nhật.</p>
                                @endif
                            </div>
                        </div>
                        <div id="noi-that" class="section tf-spacing-9 pt-0 mb-5">
                            <div class="project-detail-card">
                                <h5 class="properties-title mb_20">Nội thất</h5>
                                @if ($interiorGallery->isNotEmpty())
                                    <div class="properties-gallery project-gallery-grid">
                                        <div class="tf-grid-layout md-col-2 gap_20">
                                            <div class="img-style position-relative project-gallery-featured">
                                                <a href="{{ $displayImage($interiorGalleryGrid['featured']) }}" data-fancybox="project-interior-gallery">
                                                    <img src="{{ $displayImage($interiorGalleryGrid['featured']) }}" alt="{{ $product->title }} interior">
                                                </a>
                                            </div>
                                            <div class="project-gallery-secondary">
                                                @foreach ($interiorGalleryGrid['rows'] as $galleryRow)
                                                    <div class="wrap-img d-flex gap_20 {{ $loop->last ? '' : 'mb_20' }}">
                                                        @foreach ($galleryRow as $image)
                                                            <a href="{{ $displayImage($image) }}" data-fancybox="project-interior-gallery" class="project-gallery-secondary-item">
                                                                <img src="{{ $displayImage($image) }}" alt="{{ $product->title }} interior">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @foreach ($interiorGalleryGrid['hidden'] as $image)
                                            <a href="{{ $displayImage($image) }}" data-fancybox="project-interior-gallery" class="d-none">
                                                <img src="{{ $displayImage($image) }}" alt="{{ $product->title }} interior">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mb-0 text_secondary-color">Nội dung nội thất đang được cập nhật.</p>
                                @endif
                            </div>
                        </div>
                        <div id="can-ho" class="section tf-spacing-9 pt-0">
                            <div class="project-apartment-showcase">
                                <div class="project-apartment-showcase-heading text-center">
                                    <div class="text-uppercase project-apartment-showcase-kicker">Căn hộ</div>
                                    <h3>Có thể bạn đang tìm kiếm?</h3>
                                </div>
                                @if ($apartments->isNotEmpty())
                                    <div class="project-apartment-showcase-grid">
                                        @foreach ($apartments as $apartment)
                                            @php
                                                $apartmentImage = optional($apartment->images->first())->image;
                                                $apartmentImageUrl = $apartmentImage ? $displayImage($apartmentImage) : asset('images/section/location-7.jpg');
                                            @endphp
                                            <div class="card-house style-default h-100 hover-image-translate scrolling-effect effectFade" style="background: transparent; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding-bottom: 20px;">
                                                <a href="#apartmentDetail{{ $apartment->id }}" data-bs-toggle="offcanvas" class="img-style mb_20" style="border-radius: 16px 16px 0 0; cursor: pointer;">
                                                    <img src="{{ $apartmentImageUrl }}" width="600" height="400" alt="{{ $apartment->name }}" style="object-fit: cover; aspect-ratio: 4/3;">
                                                </a>
                                                <div class="content" style="padding: 0 20px;">
                                                    <div class="wrap-tag d-flex gap_8 mb_12 flex-wrap" style="position: absolute; top: 15px; left: 15px;">
                                                        <div class="tag sale text-button-small fw-6" style="background-color: #ff385c; color: white;">Căn hộ</div>
                                                    </div>
                                                    <h4 class="price mb_12 text-white" style="font-size: 20px;">{{ $apartment->price ? $formatPrice($apartment->price) : 'Liên hệ' }}</h4>
                                                    <a href="#apartmentDetail{{ $apartment->id }}" data-bs-toggle="offcanvas" class="title mb_8 h5 link text-white" style="font-size: 20px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; display: block; cursor: pointer;">{{ optional($product)->title ?: '...' }}</a>
                                                    <p class="text-white-50" style="font-size: 14px;">{{ $apartment->name }}</p>
                                                    <ul class="info d-flex flex-wrap mt_12" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 12px;">
                                                        <li class="d-flex align-items-center gap_8 text-title text-white fw-6" style="font-size: 14px;">
                                                            <i class="icon-Bed" style="color: #ff385c;"></i>{{ $apartment->bedroom_count ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text-white fw-6" style="font-size: 14px;">
                                                            <i class="icon-Bathstub" style="color: #ff385c;"></i>{{ $apartment->bathroom_count ?: '...' }}
                                                        </li>
                                                        <li class="d-flex align-items-center gap_8 text-title text-white fw-6" style="font-size: 14px;">
                                                            <i class="icon-Ruler" style="color: #ff385c;"></i>{{ $apartment->area ? $formatDecimal($apartment->area) . ' m2' : '...' }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @foreach ($apartments as $apartment)
                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="apartmentDetail{{ $apartment->id }}" aria-labelledby="apartmentDetailLabel{{ $apartment->id }}" style="width: 50vw; max-width: 100vw; z-index: 1060;">
                                            <div class="offcanvas-header border-bottom">
                                                <h5 class="offcanvas-title" id="apartmentDetailLabel{{ $apartment->id }}">{{ $apartment->name }}</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                @php
                                                    $aptImages = $apartment->images;
                                                @endphp
                                                @if($aptImages->isNotEmpty())
                                                    <div class="mb-4">
                                                        <div class="swiper" data-screen-xl="3" data-preview="3" data-tablet="2" data-mobile-sm="2" data-mobile="1" data-space-lg="15" data-space-md="15" data-space="15">
                                                            <div class="swiper-wrapper">
                                                                @foreach($aptImages as $img)
                                                                    <div class="swiper-slide">
                                                                        <a href="{{ $displayImage($img->image) }}" data-fancybox="gallery-apartment-{{ $apartment->id }}">
                                                                            <img src="{{ $displayImage($img->image) }}" class="d-block w-100" style="object-fit: cover; aspect-ratio: 4/3; border-radius: 12px;" alt="{{ $apartment->name }}">
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="sw-dots sw-pagination-layout text-center mt_24"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <img src="{{ asset('images/section/location-7.jpg') }}" class="d-block w-100 mb-4" style="object-fit: cover; aspect-ratio: 4/3; border-radius: 12px;" alt="No image">
                                                @endif
                                                
                                                <h4 class="mb-3" style="color: #ff385c; font-weight: 700;">{{ $apartment->price ? $formatPrice($apartment->price) : 'Liên hệ' }}</h4>
                                                
                                                <ul class="list-unstyled mb-4 d-flex gap-4 flex-wrap pb-3 border-bottom" style="font-size: 16px;">
                                                    <li class="d-flex align-items-center"><i class="icon-Bed me-2" style="color: #ff385c; font-size: 20px;"></i> {{ $apartment->bedroom_count ?: '...' }} PN</li>
                                                    <li class="d-flex align-items-center"><i class="icon-Bathstub me-2" style="color: #ff385c; font-size: 20px;"></i> {{ $apartment->bathroom_count ?: '...' }} PT</li>
                                                    <li class="d-flex align-items-center"><i class="icon-Ruler me-2" style="color: #ff385c; font-size: 20px;"></i> {{ $apartment->area ? $formatDecimal($apartment->area) . ' m2' : '...' }}</li>
                                                </ul>
                                                
                                                <div class="project-detail-content">
                                                    {!! $apartment->content ?: '<p class="text-muted">Chưa có mô tả chi tiết cho căn hộ này.</p>' !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <style>
                                        .offcanvas-backdrop {
                                            z-index: 1055 !important;
                                        }
                                        .offcanvas {
                                            z-index: 1060 !important;
                                        }
                                    </style>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var offcanvasElements = document.querySelectorAll('.offcanvas');
                                            offcanvasElements.forEach(function (offcanvasEl) {
                                                offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
                                                    var swiperEl = offcanvasEl.querySelector('.swiper');
                                                    if (swiperEl) {
                                                        if (swiperEl.swiper) {
                                                            swiperEl.swiper.update();
                                                        } else if (typeof Swiper !== 'undefined') {
                                                            new Swiper(swiperEl, {
                                                                slidesPerView: 1,
                                                                spaceBetween: 15,
                                                                observer: true,
                                                                observeParents: true,
                                                                breakpoints: {
                                                                    768: { slidesPerView: 2, spaceBetween: 15 },
                                                                    1200: { slidesPerView: 3, spaceBetween: 15 }
                                                                },
                                                                pagination: {
                                                                    el: offcanvasEl.querySelector('.sw-pagination-layout'),
                                                                    clickable: true
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                @else
                                    <p class="mb-0 text_secondary-color text-center">Danh sách căn hộ đang được cập nhật.</p>
                                @endif
                            </div>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <div class="tf-container tf-spacing-1 pt-0">
                <div class="heading-section mb_48 d-flex justify-content-between align-items-end gap_20 flex-wrap">
                    <div>
                        <h3>Dự án liên quan</h3>
                        <p class="text-body-default text_secondary-color mb-0">Những dự án khác có thể phù hợp với nhu cầu của bạn.</p>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        @php
                            $relatedImage = $relatedProduct->image ? $displayImage($relatedProduct->image) : asset('images/section/properties-details-12.jpg');
                            $relatedPrice = $formatPrice($relatedProduct->price) ?: 'Liên hệ';
                            $relatedArea = $relatedProduct->area ? $formatDecimal($relatedProduct->area) . ' m2' : $formatRange($relatedProduct->area_from, $relatedProduct->area_to, ' m2');
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="card-house style-default project-related-card h-100">
                                <a href="{{ $relatedProduct->frontend_url }}" class="img-style mb_20">
                                    <img src="{{ $relatedImage }}" alt="{{ $relatedProduct->title }}">
                                </a>
                                <div class="content">
                                    <div class="wrap-tag d-flex gap_8 mb_12 flex-wrap">
                                        <div class="tag sale text-button-small fw-6 text_primary-color">Dự án</div>
                                        @if ($relatedProduct->category)
                                            <div class="tag categoreis text-button-small fw-6 text_primary-color">
                                                {{ $relatedProduct->category->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="price mb_12">{{ $relatedPrice }}</h4>
                                    <a href="{{ $relatedProduct->frontend_url }}" class="title mb_8 h5 link text_primary-color">
                                        {{ $relatedProduct->title }}
                                    </a>
                                    <p>{{ $displayText($relatedProduct->address) }}</p>
                                    <ul class="info d-flex flex-wrap">
                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                            <i class="icon-Ruler"></i>{{ $displayText($relatedArea) }}
                                        </li>
                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                            <i class="icon-HouseSimple"></i>#{{ $relatedProduct->id }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('js/magnific-popup.min.js') }}"></script>
@endpush






