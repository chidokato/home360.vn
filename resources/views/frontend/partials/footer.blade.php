@php
    $frontendBase = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
    $footerLogo = $settings && $settings->footer_logo ? $frontendBase . '/' . ltrim($settings->footer_logo, '/') : $frontendBase . '/images/logo/logo-2.svg';
    $footerColumns = collect([
        [
            'title' => $settings->footer_column_1_title ?? null,
            'content' => $settings->footer_column_1_content ?? null,
        ],
        [
            'title' => $settings->footer_column_2_title ?? null,
            'content' => $settings->footer_column_2_content ?? null,
        ],
        [
            'title' => $settings->footer_column_3_title ?? null,
            'content' => $settings->footer_column_3_content ?? null,
        ],
        [
            'title' => $settings->footer_column_4_title ?? null,
            'content' => $settings->footer_column_4_content ?? null,
        ],
    ])->map(function ($column, $index) use ($footerLogo, $settings) {
        if (! filled($column['content'])) {
            $defaults = [
                0 => '<p class="mb_16 text_white"><img src="' . e($footerLogo) . '" alt="logo" class="main-logo footer-editor-logo"></p>',
                1 => '<ul><li><a href="' . e(route('frontend.home')) . '">Trang chủ</a></li><li><a href="' . e(route('frontend.about')) . '">Giới thiệu</a></li><li><a href="' . e(route('frontend.news.index')) . '">Tin tức</a></li><li><a href="' . e(route('frontend.contact')) . '">Liên hệ</a></li></ul>',
                2 => '<ul><li><a href="' . e(route('frontend.about')) . '">Giới thiệu</a></li><li><a href="' . e(route('frontend.home')) . '">Dự án</a></li><li><a href="' . e(route('frontend.news.index')) . '">Tin tức</a></li><li><a href="' . e(route('frontend.contact')) . '">Liên hệ</a></li></ul>',
                3 => '<p>Theo dõi thông tin mới nhất từ BDSVanPhuc.</p>',
            ];

            $column['content'] = $defaults[$index] ?? '';
        }

        if (! filled($column['title'])) {
            $defaultTitles = [
                0 => '',
                1 => '',
                2 => '',
                3 => '',
            ];

            $column['title'] = $defaultTitles[$index] ?? '';
        }

        return $column;
    });
@endphp

<footer class="footer footer-refined">
    <div class="tf-container">
        <div class="footer-body">
            <div class="row footer-grid">
                @foreach ($footerColumns as $column)
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item footer-editor-column {{ $loop->first ? 'footer-brand-column' : 'footer-info-column footer-info-column-' . $loop->iteration }}">
                            @if (filled($column['title']))
                                <div class="footer-heading text-title fw-6 text_white mb_16">{{ $column['title'] }}</div>
                            @endif
                            <div class="footer-editor-content text_color-1">
                                {!! $column['content'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="footer-bottom d-flex align-items-center justify-content-between">
            <p class="text_muted-color">&copy;{{ now()->year }} <a href="{{ route('frontend.home') }}" class="text_white hover-underline-link">BDSVanPhuc</a>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
        </path>
    </svg>
</div>

<div class="offcanvas offcanvas-end mobile-nav-wrap" tabindex="-1" id="menu-mobile" aria-labelledby="menu-mobile">
    <div class="fixed-nav-rounded-div">
        <div class="rounded-div-wrap">
            <div class="rounded-div"></div>
        </div>
    </div>
    <div class="offcanvas-header top-nav-mobile">
        <div class="offcanvas-title">
            <a href="{{ route('frontend.home') }}" class="site-logo">
                <img src="{{ $footerLogo }}" alt="logo" class="main-logo" width="193" height="44">
            </a>
        </div>
        <div data-bs-dismiss="offcanvas" class="btn-close-menu">
            <i class="icon-times-solid"></i>
        </div>
    </div>
    <div class="offcanvas-body inner-mobile-nav">
        <div class="mb-body">
            <ul id="menu-mobile-menu" class="style-1">
                <li class="menu-item">
                    <a href="{{ route('frontend.home') }}" class="item-menu-mobile">Trang chủ</a>
                </li>
                @foreach ($menuTree ?? collect() as $menu)
                    @include('frontend.partials.mobile-menu-item', ['menu' => $menu])
                @endforeach
            </ul>
            <div class="support">
                <button type="button" class="tf-btn border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                    <span>Tải bảng giá dự án</span>
                    <span class="bg-effect"></span>
                </button>
                <a href="#" class="text-need">Need help?</a>
                <ul class="mb-info">
                    <li>Call Us Now: <span class="number">{{ $settings->hotline ?? '...' }}</span></li>
                    <li>Support 24/7: <a href="mailto:{{ $settings->email ?? '' }}" class="link">{{ $settings->email ?? '...' }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
