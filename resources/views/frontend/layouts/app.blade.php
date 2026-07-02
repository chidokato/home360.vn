<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
    @php
        $frontendBase = request()->getSchemeAndHttpHost() . request()->getBaseUrl();
        $frontendBase = rtrim($frontendBase, '/');
        $seoTitle = trim((string) $__env->yieldContent('title', $pageTitle ?? ''));
        $seoDescription = trim((string) $__env->yieldContent('meta_description', $pageDescription ?? ''));
        $seoKeywords = trim((string) $__env->yieldContent('meta_keywords', $pageKeywords ?? ''));
        $seoCanonical = url()->current();
        $seoImage = $settings && $settings->logo
            ? $frontendBase . '/' . ltrim($settings->logo, '/')
            : ($settings && $settings->favicon
                ? $frontendBase . '/' . ltrim($settings->favicon, '/')
                : $frontendBase . '/images/favicon.svg');
    @endphp
    <base href="{{asset('')}}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="author" content="BDSVanPhuc">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="canonical" href="{{ $seoCanonical }}">

    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:site_name" content="{{ $settings->company_name ?? 'BDSVanPhuc' }}">
    <meta property="og:image" content="{{ $seoImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/animate.min.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/swiper-bundle.min.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/odometer.min.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/sib-styles.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/css/styles.css">
    <link rel="stylesheet" href="{{ $frontendBase }}/font/fonts.css">
    <link rel="stylesheet" type="text/css" href="{{ $frontendBase }}/icons/icomoon/style.css">
    <link rel="shortcut icon" href="{{ $settings && $settings->favicon ? $frontendBase . '/' . ltrim($settings->favicon, '/') : $frontendBase . '/images/favicon.svg' }}">
    <link rel="apple-touch-icon-precomposed" href="{{ $settings && $settings->favicon ? $frontendBase . '/' . ltrim($settings->favicon, '/') : $frontendBase . '/images/favicon.svg' }}">
    <style>
        .customer-modal .modal-content {
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(14, 22, 35, 0.24);
        }

        .customer-modal .modal-header {
            background: linear-gradient(145deg, #fffaf2 0%, #ffffff 70%);
            border-bottom: 1px solid rgba(25, 32, 45, 0.08);
        }

        .customer-modal .modal-title {
            font-size: 28px;
            line-height: 1.2;
            font-weight: 700;
        }

        .customer-modal .modal-subtitle {
            color: #697586;
            font-size: 16px;
            margin-top: 6px;
        }

        .customer-modal .customer-field label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .customer-modal .customer-field input {
            border: 1px solid #d5dbe3;
            border-radius: 12px;
            padding: 13px 16px;
            height: auto;
            transition: all 0.2s ease;
        }

        .customer-modal .customer-field input:focus {
            border-color: #d7df50;
            box-shadow: 0 0 0 4px rgba(215, 223, 80, 0.16);
        }

        .customer-modal .policy-note {
            color: #697586;
            font-size: 13px;
            line-height: 1.5;
            margin-top: 12px;
        }

        .customer-modal .policy-note a {
            color: #172554;
            font-weight: 600;
            text-decoration: underline;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="wrapper" class="counter-scroll">
        @include('frontend.partials.header')
        @yield('content')
        @include('frontend.partials.footer')
    </div>

    @php
        $customerInquiryErrors = $errors->getBag('customerInquiry');
        $customerInquiryPost = isset($product) ? $product : null;
        $customerInquiryDownloadUrl = '';

        if ($customerInquiryPost) {
            $customerInquiryDownloadUrl = collect([
                data_get($customerInquiryPost, 'price_list_url'),
                data_get($customerInquiryPost, 'brochure_url'),
                data_get($customerInquiryPost, 'download_url'),
                data_get($customerInquiryPost, 'attachment'),
                data_get($customerInquiryPost, 'file'),
            ])->filter(fn ($value) => filled($value))
                ->map(function ($value) use ($frontendBase) {
                    $value = trim((string) $value);

                    if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                        return $value;
                    }

                    return $frontendBase . '/' . ltrim($value, '/');
                })
                ->first() ?? '';
        }
    @endphp

    <div class="modal fade customer-modal" id="customer-info-modal" tabindex="-1" aria-labelledby="customer-info-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header px-5 py-5 border-0">
                    <div>
                        <h5 class="modal-title text_primary-color" id="customer-info-modal-label">Th&ocirc;ng tin kh&aacute;ch h&agrave;ng</h5>
                        <p class="mb-0 modal-subtitle">Vui l&ograve;ng &#273;&#7875; l&#7841;i th&ocirc;ng tin, ch&uacute;ng t&ocirc;i s&#7869; li&ecirc;n h&#7879; v&#7899;i b&#7841;n s&#7899;m nh&#7845;t.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5 pb-5 pt-0">
                    @if (session('customer_inquiry_success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('customer_inquiry_success') }}
                        </div>
                    @endif

                    <form action="{{ route('frontend.customer-inquiries.store') }}" method="POST" class="d-grid gap_16">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ old('post_id', $customerInquiryPost?->id) }}">
                        <input type="hidden" name="source_url" value="{{ old('source_url', request()->fullUrl()) }}">
                        <input type="hidden" name="download_url" value="{{ old('download_url', $customerInquiryDownloadUrl) }}">
                        <div class="customer-field">
                            <label for="customer-name" class="text-button text_primary-color mb_8">H&#7885; v&agrave; t&ecirc;n</label>
                            <fieldset>
                                <input type="text" id="customer-name" name="name" value="{{ old('name') }}" placeholder="Nh&#7853;p h&#7885; v&agrave; t&ecirc;n">
                            </fieldset>
                            @if ($customerInquiryErrors->has('name'))
                                <div class="text-danger small mt-2">{{ $customerInquiryErrors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="customer-field">
                            <label for="customer-phone" class="text-button text_primary-color mb_8">S&#7889; &#273;i&#7879;n tho&#7841;i</label>
                            <fieldset>
                                <input type="text" id="customer-phone" name="phone" value="{{ old('phone') }}" placeholder="Nh&#7853;p s&#7889; &#273;i&#7879;n tho&#7841;i">
                            </fieldset>
                            @if ($customerInquiryErrors->has('phone'))
                                <div class="text-danger small mt-2">{{ $customerInquiryErrors->first('phone') }}</div>
                            @endif
                        </div>
                        <div class="customer-field">
                            <label for="customer-email" class="text-button text_primary-color mb_8">Email</label>
                            <fieldset>
                                <input type="email" id="customer-email" name="email" value="{{ old('email') }}" placeholder="Nh&#7853;p email">
                            </fieldset>
                            @if ($customerInquiryErrors->has('email'))
                                <div class="text-danger small mt-2">{{ $customerInquiryErrors->first('email') }}</div>
                            @endif
                        </div>
                        <button type="submit" class="tf-btn border-0 w-100">
                            <span>T&#7843;i xu&#7889;ng</span>
                            <span class="bg-effect"></span>
                        </button>
                        <p class="policy-note mb-0">
                            ( * ) B&#7857;ng vi&#7879;c nh&#7845;n v&agrave;o n&uacute;t "t&#7843;i xu&#7889;ng", qu&yacute; kh&aacute;ch &#273;&#7891;ng &yacute; v&#7899;i <a href="https://bdsvanphuc.org/noi-bo/chinh-sach-bao-mat-thong-tin-khach-hang">ch&iacute;nh s&aacute;ch b&#7843;o m&#7853;t th&ocirc;ng tin</a> c&#7911;a ch&uacute;ng t&ocirc;i
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ $frontendBase }}/js/bootstrap.min.js"></script>
    <script src="{{ $frontendBase }}/js/jquery.min.js"></script>
    <script src="{{ $frontendBase }}/js/splitting.min.js"></script>
    <script src="{{ $frontendBase }}/js/swiper-bundle.min.js"></script>
    <script src="{{ $frontendBase }}/js/carousel.js"></script>
    <script src="{{ $frontendBase }}/js/jquery.nice-select.min.js"></script>
    <script src="{{ $frontendBase }}/js/odometer.min.js"></script>
    <script src="{{ $frontendBase }}/js/counter.js"></script>
    <script src="{{ $frontendBase }}/js/parallaxie.js"></script>
    <script src="{{ $frontendBase }}/js/infinityslide.js"></script>
    <script src="{{ $frontendBase }}/js/ScrollSmooth.js"></script>
    <script src="{{ $frontendBase }}/js/ScrollTrigger.min.js"></script>
    <script src="{{ $frontendBase }}/js/SplitText.min.js"></script>
    <script src="{{ $frontendBase }}/js/gsap.min.js"></script>
    <script src="{{ $frontendBase }}/js/handleGsap.js"></script>
    <script src="{{ $frontendBase }}/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('customer-info-modal');

            if (!modalElement || typeof bootstrap === 'undefined') {
                return;
            }

            const shouldOpenModal = @json($customerInquiryErrors->any() || session()->has('customer_inquiry_success'));
            const downloadUrl = @json(session('customer_inquiry_download_url'));

            if (shouldOpenModal) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }

            if (downloadUrl) {
                window.open(downloadUrl, '_blank', 'noopener');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
