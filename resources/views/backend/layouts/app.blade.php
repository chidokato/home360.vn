<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Admin') | BDSVanPhuc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin dashboard" name="description" />
    <meta content="BDSVanPhuc" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('admin-assets/images/favicon.ico') }}">
    <script src="{{ asset('admin-assets/js/layout.js') }}"></script>
    <link href="{{ asset('admin-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/css/backend-admin.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
</head>
<body>
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('backend.admin.dashboard') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('admin-assets/images/logo-sm.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('admin-assets/images/logo-dark.png') }}" alt="" height="17">
                                </span>
                            </a>
                            <a href="{{ route('backend.admin.dashboard') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('admin-assets/images/logo-sm.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('admin-assets/images/logo-light.png') }}" alt="" height="17">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                        <form class="app-search d-none d-md-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search..." autocomplete="off">
                                <span class="mdi mdi-magnify search-widget-icon"></span>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search...">
                                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{ asset('admin-assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Administrator</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
                                <a class="dropdown-item" href="{{ route('backend.users.index') }}">
                                    <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">User</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('backend.admin.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                    <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="app-menu navbar-menu">
            <div class="navbar-brand-box">
                <a href="{{ route('backend.admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('admin-assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admin-assets/images/logo-dark.png') }}" alt="" height="17">
                    </span>
                </a>
                <a href="{{ route('backend.admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('admin-assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admin-assets/images/logo-light.png') }}" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span>Menu</span></li>
                        @php
                            $menuItems = [
                                ['label' => 'Menu', 'icon' => 'ri-menu-line', 'route' => 'backend.menus.index'],
                                ['label' => 'Pages', 'icon' => 'ri-pages-line', 'route' => 'backend.pages.index'],
                                ['label' => 'Category', 'icon' => 'ri-list-check-2', 'route' => 'backend.categories.index'],
                                ['label' => 'Product', 'icon' => 'ri-shopping-bag-3-line', 'route' => 'backend.products.index'],
                                ['label' => 'Apartment', 'icon' => 'ri-building-4-line', 'route' => 'backend.apartments.index'],
                                ['label' => 'Customer inquiry', 'icon' => 'ri-customer-service-2-line', 'route' => 'backend.customer-inquiries.index'],
                                ['label' => 'Tinh thanh', 'icon' => 'ri-road-map-line', 'route' => 'backend.administrative-units.provinces'],
                                ['label' => 'Phuong xa', 'icon' => 'ri-community-line', 'route' => 'backend.administrative-units.wards'],
                                ['label' => 'News', 'icon' => 'ri-newspaper-line', 'route' => 'backend.news.index'],
                                ['label' => 'SEO', 'icon' => 'ri-search-eye-line', 'route' => 'backend.seo.edit'],
                                ['label' => 'Setting', 'icon' => 'ri-settings-3-line', 'route' => 'backend.settings.edit'],
                                ['label' => 'User', 'icon' => 'ri-user-3-line', 'route' => 'backend.users.index'],
                            ];
                        @endphp
                        @foreach ($menuItems as $item)
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ $item['label'] === 'Menu' && request()->routeIs('backend.menus.*') ? 'active' : '' }} {{ $item['label'] === 'Pages' && request()->routeIs('backend.pages.*') ? 'active' : '' }} {{ $item['label'] === 'User' && request()->routeIs('backend.users.*') ? 'active' : '' }} {{ $item['label'] === 'Category' && request()->routeIs('backend.categories.*') ? 'active' : '' }} {{ $item['label'] === 'Product' && request()->routeIs('backend.products.*') ? 'active' : '' }} {{ $item['label'] === 'Apartment' && request()->routeIs('backend.apartments.*') ? 'active' : '' }} {{ $item['label'] === 'Customer inquiry' && request()->routeIs('backend.customer-inquiries.*') ? 'active' : '' }} {{ $item['label'] === 'Tinh thanh' && request()->routeIs('backend.administrative-units.provinces') ? 'active' : '' }} {{ $item['label'] === 'Phuong xa' && request()->routeIs('backend.administrative-units.wards') ? 'active' : '' }} {{ $item['label'] === 'News' && request()->routeIs('backend.news.*') ? 'active' : '' }} {{ $item['label'] === 'SEO' && request()->routeIs('backend.seo.*') ? 'active' : '' }} {{ $item['label'] === 'Setting' && request()->routeIs('backend.settings.*') ? 'active' : '' }}" href="{{ $item['route'] ? route($item['route']) : '#' }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="sidebar-background"></div>
        </div>

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">@yield('page_title', 'Dashboard')</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="{{ route('backend.admin.dashboard') }}">Admin</a></li>
                                        <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    @yield('content')
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            {{ now()->year }} © BDSVanPhuc.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Admin powered by Velzon
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <form id="admin-logout-form" action="{{ route('backend.admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <div
        id="backend-app-config"
        class="d-none"
        data-upload-url="{{ route('backend.admin.uploads.editor-image') }}"
        data-success-message="{{ session('success', '') }}"
        data-error-message="{{ session('error', '') }}"
        data-validation-message="{{ $errors->any() ? collect($errors->all())->implode(' | ') : '' }}"
    ></div>

    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <script src="{{ asset('admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script src="{{ asset('admin-assets/js/customc-keditor.js') }}"></script>
    <script src="{{ asset('admin-assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('admin-assets/js/app.js') }}"></script>
    <script src="{{ asset('admin-assets/js/backend-admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
