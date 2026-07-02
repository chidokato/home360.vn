@extends('frontend.layouts.app')

@section('title', $pageTitle ?? 'Liên hệ')
@section('meta_description', $pageDescription ?? 'Thông tin liên hệ')

@push('styles')
    <style>
        .contact-page {
            padding-top: 118px;
            padding-bottom: 72px;
            background: linear-gradient(180deg, #f6f8fb 0%, #ffffff 34%);
        }

        .contact-hero {
            border-radius: 22px;
            padding: 28px 30px;
            margin-bottom: 28px;
            background: radial-gradient(1200px 300px at 0% 0%, rgba(215, 223, 80, 0.22), transparent 60%), #ffffff;
            border: 1px solid rgba(24, 34, 48, 0.08);
        }

        .contact-hero h1 {
            margin-bottom: 8px;
            font-size: clamp(32px, 4vw, 48px);
        }

        .contact-hero .crumb {
            color: #5f6b7a;
            font-size: 15px;
        }

        .contact-card {
            height: 100%;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid rgba(24, 34, 48, 0.08);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            padding: 28px;
        }

        .contact-card h5 {
            margin-bottom: 16px;
        }

        .contact-list li {
            color: #2c3746;
            line-height: 1.65;
        }

        .contact-list li + li {
            margin-top: 10px;
        }

        .contact-list strong {
            color: #0f1724;
        }

        .contact-form .sub {
            letter-spacing: 0.08em;
        }

        .contact-form .heading-section h3 {
            margin-bottom: 0;
        }

        .contact-form input,
        .contact-form textarea {
            border-radius: 12px;
            border: 1px solid #d8dee8;
            padding: 13px 16px;
            transition: all 0.2s ease;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: #d7df50;
            box-shadow: 0 0 0 4px rgba(215, 223, 80, 0.16);
        }

        .contact-form .tf-btn {
            min-width: 180px;
            border-radius: 12px;
        }

        @media (max-width: 991.98px) {
            .contact-page {
                padding-top: 98px;
                padding-bottom: 56px;
            }

            .contact-hero {
                padding: 22px 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="contact-page">
        <div class="tf-container">
            <section class="contact-hero">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap_12">
                    <div>
                        <h1 class="text_primary-color">Liên hệ</h1>
                        <p class="mb-0 text_secondary-color">Kết nối với chúng tôi để nhận tư vấn nhanh và chính xác.</p>
                    </div>
                    <div class="crumb">
                        <a href="{{ route('frontend.home') }}">Trang chủ</a> / <span>Liên hệ</span>
                    </div>
                </div>
            </section>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="contact-card">
                        <h5 class="title">Thông tin công ty</h5>
                        <ul class="contact-list list-unstyled mb-0">
                            <li><strong>Tên công ty:</strong> {{ $settings->company_name ?? 'BDSVanPhuc' }}</li>
                            <li><strong>Địa chỉ:</strong> {{ $settings->address ?? '...' }}</li>
                            <li><strong>Email:</strong> {{ $settings->email ?? '...' }}</li>
                            <li><strong>Hotline:</strong> {{ $settings->hotline ?? '...' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="contact-card contact-form">
                        <div class="heading-section mb_30">
                            <span class="sub text-uppercase fw-6 text_secondary-color-2">Contact</span>
                            <h3>Gửi thông tin liên hệ</h3>
                        </div>
                        <form class="row">
                            <div class="col-md-6">
                                <fieldset class="mb_20">
                                    <input type="text" placeholder="Họ tên">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="mb_20">
                                    <input type="email" placeholder="Email">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="mb_20">
                                    <input type="text" placeholder="Số điện thoại">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="mb_20">
                                    <input type="text" placeholder="Chủ đề">
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <fieldset class="mb_20">
                                    <textarea rows="7" placeholder="Nội dung"></textarea>
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <button type="button" class="tf-btn">
                                    <span>Gửi liên hệ</span>
                                    <span class="bg-effect"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
