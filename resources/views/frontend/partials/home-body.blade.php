@php
    $heroProducts = ($featuredProducts ?? collect())->take(3)->values();
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
    $heroImage = static fn ($product, $fallback) => asset(ltrim($product->image ?: $fallback, '/'));
    $heroPrice = static fn ($product) => $formatPrice($product->price) ?: 'Liên hệ';
    $heroArea = static fn ($product) => $product->area ? $formatDecimal($product->area) . ' m2' : $formatRange($product->area_from, $product->area_to, ' m2');
    $heroBedrooms = static fn ($product) => filled($product->bedroom_count) ? $product->bedroom_count . ' PN' : ($formatRange($product->bedroom_count_from, $product->bedroom_count_to, 'PN') ?: '...');
    $heroBathrooms = static fn ($product) => filled($product->bathroom_count) ? $product->bathroom_count . ' WC' : ($formatRange($product->bathroom_count_from, $product->bathroom_count_to, ' WC') ?: '...');
    $latestHomeProducts = ($latestProducts ?? collect())->take(3)->values();
    $latestHomeImage = static fn ($path, $fallback) => asset(ltrim($path ?: $fallback, '/'));
    $latestHomePrice = static fn ($product) => $formatPrice($product->price) ?: 'Liên hệ';
    $latestHomeArea = static fn ($product) => $product->area ? $formatDecimal($product->area) . ' m2' : ($formatRange($product->area_from, $product->area_to, ' m2') ?: '...');
    $latestHomeBedrooms = static fn ($product) => filled($product->bedroom_count) ? $product->bedroom_count . ' PN' : ($formatRange($product->bedroom_count_from, $product->bedroom_count_to, ' PN') ?: '...');
    $latestHomeBathrooms = static fn ($product) => filled($product->bathroom_count) ? $product->bathroom_count . ' WC' : ($formatRange($product->bathroom_count_from, $product->bathroom_count_to, ' WC') ?: '...');
    $latestNewsItems = ($latestNews ?? collect())->take(3)->values();
    $latestNewsImage = static fn ($post, $fallback) => asset(ltrim(($post->image ?: $fallback), '/'));
    $latestNewsDate = static fn ($post) => optional($post->published_at)->format('M d, Y') ?: '...';
    $locationProjects = ($locationProjects ?? collect())->take(4)->values();
@endphp

<!-- page-title -->
        <div class="page-title style-5 sw-layout">
            <div class="page-inner">
                <div class="swiper effect-content-slide" data-autoplay="false">
                    <div class="swiper-wrapper">
                        @forelse ($heroProducts as $index => $product)
                            <div class="swiper-slide">
                                <div class="slide-inner">
                                    <div class="thumbs effect-img-zoom ">
                                        <img class="img-zoom" loading="eager" decoding="async"
                                            src="{{ $heroImage($product, $index % 2 === 0 ? 'images/page-title/page-title-10.jpg' : 'images/page-title/page-title-11.jpg') }}"
                                            width="1920" height="760" alt="{{ $product->title }}">
                                    </div>
                                    <div class="content effect-left effect-item effect-1">
                                        <div class="tf-container">
                                            <div class="row justify-content-end">
                                                <div class="col-lg-5 col-sm-9">
                                                    <div class="content-inner">
                                                        <div class="wrap-tag d-flex gap_8 mb_12 effect-left effect-item effect-3">
                                                            <div class="tag sale text-button-small fw-6 text_primary-color">
                                                                Dự án nổi bật
                                                            </div>
                                                            @if ($product->category)
                                                                <div class="tag categoreis text-button-small fw-6 text_primary-color">
                                                                    {{ $product->category->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <h4 class="price mb_12 effect-left effect-item effect-4">
                                                            {{ $heroPrice($product) }}
                                                        </h4>
                                                        <h4 class="title mb_8 effect-left effect-item effect-5"><a href="{{ $product->frontend_url }}">{{ $product->title }}</a></h4>
                                                        <p class="effect-left effect-item effect-6">{{ $product->address ?: '...' }}</p>
                                                        <ul class="info d-flex effect-up effect-item effect-7">
                                                            <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                                <i class="icon-Bed"></i>{{ $heroBedrooms($product) }}
                                                            </li>
                                                            <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                                <i class="icon-Bathstub"></i>{{ $heroBathrooms($product) }}
                                                            </li>
                                                            <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                                                <i class="icon-Ruler"></i>{{ $heroArea($product) ?: '...' }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="slide-inner">
                                    <div class="thumbs effect-img-zoom ">
                                        <img class="img-zoom" loading="eager" decoding="async"
                                            src="images/page-title/page-title-10.jpg" width="1920" height="760"
                                            alt="page-title">
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="sw-button nav-prev-layout lg-hide">
                        <i class="icon-CaretLeft"></i>
                    </div>
                    <div class="sw-button nav-next-layout lg-hide">
                        <i class="icon-CaretRight"></i>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- End page-title -->

        <!-- main-content -->
        <div class="main-content">
            <!-- section-features-property -->
            <div class="bg-light-color tf-spacing-1">
                <div class="tf-container w-1830">
                    <div class="heading-section justify-content-center text-center mb_40">
                        <span class="sub text-uppercase fw-6 text_secondary-color-2 split-text effect-rotate">Dự án mới nhất</span>
                        <h3 class="split-text effect-blur-fade">Những sản phẩm dành cho bạn !!</h3>
                    </div>
                    <div class="d-grid gap_30">
                        @forelse ($latestHomeProducts as $index => $product)
                            @php
                                $detailUrl = $product->frontend_url;
                                $fallbackIndex = ($index % 3) + 1;
                                $galleryImages = $product->galleryImages->pluck('image')->filter()->values();
                                $mainImage = $latestHomeImage($product->image, 'images/home/home-list-' . $fallbackIndex . '.jpg');
                                $subImageOne = $latestHomeImage($galleryImages->get(0), 'images/home/home-list-' . $fallbackIndex . '.1.jpg');
                                $subImageTwo = $latestHomeImage($galleryImages->get(1), 'images/home/home-list-' . $fallbackIndex . '.2.jpg');
                            @endphp
                            <div class="card-house style-list v1 scrolling-effect effectBottom">
                                <div class="wrap-img">
                                    <a href="{{ $detailUrl }}" class="img-style">
                                        <img loading="lazy" decoding="async" src="{{ $mainImage }}"
                                            srcset="{{ $mainImage }} 600w"
                                            sizes="(max-width: 600px) 100vw, 600px" width="600" height="300" alt="{{ $product->title }}">
                                    </a>
                                    <a href="{{ $detailUrl }}" class="img-style">
                                        <img loading="lazy" decoding="async" src="{{ $subImageOne }}"
                                            srcset="{{ $subImageOne }} 300w"
                                            sizes="(max-width: 300px) 100vw, 300px" width="300" height="300" alt="{{ $product->title }}">
                                    </a>
                                    <a href="{{ $detailUrl }}" class="img-style">
                                        <img loading="lazy" decoding="async" src="{{ $subImageTwo }}"
                                            srcset="{{ $subImageTwo }} 300w"
                                            sizes="(max-width: 300px) 100vw, 300px" width="300" height="300" alt="{{ $product->title }}">
                                    </a>
                                </div>
                                <div class="content">
                                    <div class="d-flex align-items-center gap_12 mb_16 flex-wrap justify-content-between">
                                        <h4 class="price ">{{ $latestHomePrice($product) }}</h4>
                                        <div class="wrap-tag d-flex gap_8">
                                            <div class="tag rent text-button-small fw-6 text_primary-color">
                                                Mới nhất
                                            </div>
                                            <div class="tag categoreis text-button-small fw-6 text_primary-color">
                                                {{ $product->category->name ?? 'Dự án' }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ $detailUrl }}" class="title mb_8 h5 link text_primary-color ">{{ $product->title }}</a>
                                    <p>{{ $product->address ?: '...' }}</p>
                                    <ul class="info d-flex">
                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                            <i class="icon-Bed"></i>{{ $latestHomeBedrooms($product) }}
                                        </li>
                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                            <i class="icon-Bathstub"></i>{{ $latestHomeBathrooms($product) }}
                                        </li>
                                        <li class="d-flex align-items-center gap_8 text-title text_primary-color fw-6">
                                            <i class="icon-Ruler"></i>{{ $latestHomeArea($product) }}
                                        </li>
                                    </ul>
                                    
                                </div>
                            </div>
                        @empty
                            <div class="card-house style-list v1 scrolling-effect effectBottom">
                                <div class="content">
                                    <div class="d-flex align-items-center gap_12 mb_16 flex-wrap justify-content-between">
                                        <h4 class="price ">Chưa có dự án</h4>
                                    </div>
                                    <p>Nội dung sẽ được cập nhật sớm.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- End section-features-property -->

            <!-- section-location -->
            <div class="section-location-3 sw-layout tf-spacing-1">
                <div class="tf-container w-1830">
                    <div class="heading-section justify-content-center text-center mb_46">
                        <span class="sub text-uppercase fw-6 text_secondary-color-2 split-text effect-rotate">Khu vực dự án</span>
                        <h3 class="split-text effect-blur-fade">Vị trí nổi bật</h3>
                    </div>
                    <div class="position-relative">
                        <div class="swiper scrolling-effect effectLeft" data-preview="4" data-tablet="2"
                            data-mobile-sm="2" data-mobile="1" data-space-lg="30" data-space-md="20" data-space="15">
                            <div class="swiper-wrapper">
                                @forelse ($locationProjects as $index => $location)
                                    @php
                                        $locationFallback = 'images/section/location-' . (7 + $index) . '.jpg';
                                        $locationImage = $latestHomeImage($location->image ?? null, $locationFallback);
                                        $product = (object) [
                                            'address' => $location->name ?? null,
                                            'category' => null,
                                            'unit_count_to' => null,
                                            'unit_count_from' => null,
                                        ];
                                        $locationLabel = $product->address ?: ($product->category->name ?? '...');
                                        $locationCount = filled($product->unit_count_to)
                                            ? $product->unit_count_to . ' căn'
                                            : (filled($product->unit_count_from) ? 'Từ ' . $product->unit_count_from . ' căn' : 'Dự án nổi bật');
                                        $locationLabel = $location->name ?? '...';
                                        $locationCount = (($location->projects_count ?? 0) > 0)
                                            ? number_format((int) $location->projects_count, 0, ',', '.') . ' dự án'
                                            : 'Chưa có dữ liệu';
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="location-item style-1 hover-image">
                                            <a href="{{ $location->frontend_url ?? '#' }}" class="img-style">
                                                <img loading="lazy" decoding="async" width="428" height="590"
                                                    src="{{ $locationImage }}" alt="{{ $locationLabel }}">
                                            </a>
                                            <div class="content">
                                                <a href="{{ $location->frontend_url ?? '#' }}" class="mb_8 h5 text_primary-color">{{ $locationLabel }}</a>
                                                <p class="text-caption-1">{{ $locationCount }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <div class="location-item style-1 hover-image">
                                            <a href="#" class="img-style">
                                                <img loading="lazy" decoding="async" width="428" height="590"
                                                    src="images/section/location-7.jpg" alt="location">
                                            </a>
                                            <div class="content">
                                                <span class="mb_8 h5 text_primary-color d-block">...</span>
                                                <p class="text-caption-1">Chưa có dữ liệu</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="sw-dots style-1 sw-pagination-layout text-center mt_24 d-xl-none">
                            </div>
                        </div>
                        <div class="sw-button nav-prev-layout xl-hide">
                            <i class="icon-CaretLeft"></i>
                        </div>
                        <div class="sw-button nav-next-layout xl-hide">
                            <i class="icon-CaretRight"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End section-location -->

            <!-- section-categories -->
            <div class="sw-layout bg-dark-color tf-spacing-1">
                <div class="tf-container w-1830">
                    <div class="heading-section justify-content-center text-center mb_48">
                        <span
                            class="sub text-uppercase fw-6 text_secondary-color-2 split-text effect-rotate">Căn hộ</span>
                        <h3 class="text_white split-text effect-blur-fade">Có thể bạn đang tìm kiếm ?</h3>
                    </div>
                    <div class="swiper" data-screen-xl="5" data-preview="5" data-tablet="3" data-mobile-sm="2"
                        data-mobile="1" data-space-lg="30" data-space-md="20" data-space="15">
                        <div class="swiper-wrapper">
                            @php
                                $apartmentsList = $apartments ?? collect();
                            @endphp
                            @forelse ($apartmentsList as $apartment)
                                @php
                                    $detailUrl = $apartment->project ? $apartment->project->frontend_url . '#can-ho' : '#';
                                    $fallbackImage = 'images/home/home-list-' . (($loop->index % 3) + 1) . '.jpg';
                                    $apartmentImage = optional($apartment->images->first())->image;
                                    $mainImage = $latestHomeImage($apartmentImage, $fallbackImage);
                                @endphp
                                <div class="swiper-slide">
                                    <div class="card-house style-default h-100 hover-image-translate scrolling-effect effectFade" style="background: transparent; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding-bottom: 20px;">
                                        <a href="{{ $detailUrl }}" class="img-style mb_20" style="border-radius: 16px 16px 0 0;">
                                            <img src="{{ $mainImage }}" width="600" height="400" alt="{{ $apartment->name }}" style="object-fit: cover; aspect-ratio: 4/3;">
                                        </a>
                                        <div class="content" style="padding: 0 20px;">
                                            <div class="wrap-tag d-flex gap_8 mb_12 flex-wrap" style="position: absolute; top: 15px; left: 15px;">
                                                <div class="tag sale text-button-small fw-6" style="background-color: #ff385c; color: white;">Căn hộ</div>
                                            </div>
                                            <h4 class="price mb_12 text-white" style="font-size: 20px;">{{ $apartment->price ? $formatPrice($apartment->price) : 'Liên hệ' }}</h4>
                                            <a href="{{ $detailUrl }}" class="title mb_8 h5 link text-white" style="font-size: 20px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; display: block;">{{ optional($apartment->project)->title ?: '...' }}</a>
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
                                </div>
                            @empty
                                <div class="swiper-slide" style="width: 100%;">
                                    <p class="text-white text-center py-5">Đang cập nhật danh sách căn hộ...</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="sw-dots sw-pagination-layout text-center mt_24">
                        </div>
                    </div>
                </div>
            </div>
            <!-- End section-categories -->

            <!-- section-latest-new -->
            <div class="sw-layout tf-spacing-1">
                <div class="tf-container">
                    <div class="heading-section justify-content-center text-center mb_48">
                        <span class="sub text-uppercase fw-6 text_secondary-color-2 split-text effect-rotate">Cập nhật mới nhất</span>
                        <h3 class="split-text effect-blur-fade">Tin tức thị trường bất động sản !</h3>
                    </div>
                    <div class="swiper " data-preview="3" data-tablet="2" data-mobile-sm="2" data-mobile="1"
                        data-space-lg="30" data-space-md="20" data-space="15">
                        <div class="swiper-wrapper ">
                            @forelse ($latestNewsItems as $index => $post)
                                @php
                                    $newsUrl = $post->frontend_url;
                                    $fallbackImage = match ($index) {
                                        1 => 'images/blog/blog-item-10.jpg',
                                        2 => 'images/blog/blog-item-2.jpg',
                                        default => 'images/blog/blog-item-3.jpg',
                                    };
                                @endphp
                                <div class="swiper-slide">
                                    <div class="blog-article-item style-default hover-image-translate">
                                        <div class="article-thumb image-wrap mb_24">
                                            <img loading="lazy" decoding="async" src="{{ $latestNewsImage($post, $fallbackImage) }}"
                                                width="850" height="478" alt="{{ $post->title }}">
                                            <a href="{{ $newsUrl }}" class="tag text-label text text_primary-color text-uppercase">
                                                {{ $post->category->name ?? 'Tin tức' }}
                                            </a>
                                            <a href="{{ $newsUrl }}" class="overlay-link"></a>
                                        </div>
                                        <div class="article-content ">
                                            <div class="meta-post d-flex align-items-center mb_12">
                                                <div class="item text_secondary-color text-caption-1 ">Post By <a href="{{ $newsUrl }}"
                                                        class="link text_primary-color">BDSVanPhuc</a></div>
                                                <div class="item text_secondary-color text-caption-1 ">{{ $latestNewsDate($post) }}</div>
                                            </div>
                                            <h5 class="title ">
                                                <a href="{{ $newsUrl }}" class=" hover-line-text">{{ $post->title }}</a>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <div class="blog-article-item style-default hover-image-translate">
                                        <div class="article-content ">
                                            <h5 class="title ">
                                                <a href="#" class=" hover-line-text">Chưa có tin tức mới</a>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="sw-dots style-1 sw-pagination-layout text-center mt_24">
                        </div>
                    </div>
                </div>
            </div>
            <!-- End section-latest-new -->
        </div>
        <!-- End main-content -->
