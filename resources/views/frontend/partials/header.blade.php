@php
    $frontendBase = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
    $logo = $settings && $settings->logo ? $frontendBase . '/' . ltrim($settings->logo, '/') : $frontendBase . '/images/logo/logo.svg';
    $isHome = request()->routeIs('frontend.home');
@endphp

@if ($isHome)
    <header class="header style-default header-sticky">
        <div class="tf-container w-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="header-inner">
                        <a href="{{ route('frontend.home') }}" class="site-logo">
                            <img class="logo_header" alt="logo" width="292" height="48" src="{{ $logo }}">
                        </a>
                        @include('frontend.partials.header-nav')
                        <div class="header-right d-flex align-items-center gap_20">
                            <button type="button" class="tf-btn border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                                <span>Tải bảng giá dự án</span>
                                <span class="bg-effect"></span>
                            </button>
                            <div class="mobile-button d-xl-none" data-bs-toggle="offcanvas"
                                data-bs-target="#menu-mobile" aria-controls="menu-mobile">
                                <div class="burger">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <header class="header style-3">
        <div class="tf-container w-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="header-inner">
                        <a href="{{ route('frontend.home') }}" class="site-logo">
                            <img class="logo_header" alt="logo" width="292" height="48" src="{{ $logo }}">
                        </a>
                        @include('frontend.partials.header-nav')
                        <div class="header-right d-flex align-items-center gap_20">
                            <button type="button" class="tf-btn md-hide border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                                <span>Tải bảng giá dự án</span>
                                <span class="bg-effect"></span>
                            </button>
                            <div class="mobile-button d-xl-none" data-bs-toggle="offcanvas"
                                data-bs-target="#menu-mobile" aria-controls="menu-mobile">
                                <div class="burger">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@else
    <header class="header style-default" style="position: sticky; top: 0; z-index: 999; background: #fff;">
        <div class="tf-container w-1890">
            <div class="row">
                <div class="col-12">
                    <div class="header-inner">
                        <a href="{{ route('frontend.home') }}" class="site-logo">
                            <img class="logo_header" alt="logo" width="292" height="48" src="{{ $logo }}">
                        </a>
                        @include('frontend.partials.header-nav')
                        <div class="header-right d-flex align-items-center gap_20">
                            <button type="button" class="tf-btn border-0" data-bs-toggle="modal" data-bs-target="#customer-info-modal">
                                <span>Tải bảng giá dự án</span>
                                <span class="bg-effect"></span>
                            </button>
                            <div class="mobile-button d-xl-none" data-bs-toggle="offcanvas"
                                data-bs-target="#menu-mobile" aria-controls="menu-mobile">
                                <div class="burger">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

@endif
