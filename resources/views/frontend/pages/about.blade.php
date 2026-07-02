@extends('frontend.layouts.app')

@section('title', $pageTitle ?? 'Giới thiệu')
@section('meta_description', $pageDescription ?? 'Thông tin giới thiệu doanh nghiệp')

@section('content')
    <div class="main-content">
        <div class="section-about tf-spacing-1">
            <div class="tf-container">
                <div class="heading-section justify-content-center text-center mb_48">
                    <span class="sub text-uppercase fw-6">Giới thiệu công ty</span>
                    <h3>Đơn vị phát triển và phân phối<br>bất động sản chuyên nghiệp</h3>
                </div>
                <div class="parallaxie" style='background: url("images/section/section-about.jpg")'>
                    <div class="content">
                        <div class="wrap d-flex flex-column">
                            <div class="tf-box-icon style-1">
                                <div class="heading d-flex justify-content-between mb_19">
                                    <div class="counter-item style-default">
                                        <div class="counter-number">
                                            <h2 class="odometer" data-number="100">10</h2>
                                        </div>
                                    </div>
                                    <div class="icon">
                                        <i class="icon-Certificate"></i>
                                    </div>
                                </div>
                                <h6 class="text_secondary-color sub">Sản phẩm ++</h6>
                            </div>
                            <div class="d-flex gap_12 bot">
                                <div class="tf-box-icon style-1">
                                    <div class="heading d-flex justify-content-between mb_19">
                                        <div class="counter-item style-default">
                                            <div class="counter-number">
                                                <h2 class="odometer" data-number="1000">10</h2>
                                            </div>
                                        </div>
                                        <div class="icon">
                                            <i class="icon-BuildingOffice"></i>
                                        </div>
                                    </div>
                                    <h6 class="text_secondary-color sub">Giao dịch thành công ++</h6>
                                </div>
                                <div class="tf-box-icon style-1">
                                    <div class="heading d-flex justify-content-between mb_19">
                                        <div class="counter-item style-default">
                                            <div class="counter-number">
                                                <h2 class="odometer" data-number="500">1</h2>
                                            </div>
                                        </div>
                                        <div class="icon">
                                            <i class="icon-ChartDonut"></i>
                                        </div>
                                    </div>
                                    <h6 class="text_secondary-color sub">Nhân sự ++</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tf-grid-layout md-col-2">
                    <div class="box">
                        <h4 class="title d-flex gap_12 align-items-center mb_20"><i class="icon-Crown"></i>Sứ mệnh</h4>
                        <p class="mb_8">Cung cấp sản phẩm và dịch vụ bất động sản ưu việt, nâng cao chất lượng sống và
                            gia tăng giá trị cho khách hàng, nhà đầu tư.</p>
                        <p>Mang đến dịch vụ chuyên nghiệp, giải pháp linh hoạt và tuân thủ chuẩn mực đạo đức kinh doanh,
                            trách nhiệm xã hội trong mọi hoạt động.</p>
                    </div>
                    <div class="box">
                        <h4 class="title d-flex gap_12 align-items-center mb_20"><i class="icon-Target"></i>Tầm nhìn</h4>
                        <p class="mb_8">Trở thành đơn vị năng động trong phân phối, cho thuê và quản lý bất động sản tại
                            Việt Nam và quốc tế.</p>
                        <p>Tiên phong chuyển đổi số trong kinh doanh bất động sản, tạo ra giá trị vượt trội và trở thành
                            lựa chọn hàng đầu của khách hàng.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-history tf-spacing-1">
            <div class="t-container">
                <div class="heading-section justify-content-center text-center mb_48">
                    <span class="sub text-uppercase fw-6 text_secondary-color-2">Hành trình phát triển</span>
                    <h3>Các dấu mốc nổi bật</h3>
                </div>
            </div>
            <div class="content">
                <div class="tf-container w-xl">
                    <div class="wrap-time-line">
                        <div class="time-item">
                            <div class="heading">
                                <h3 class="mb_8">2006 - 2020</h3>
                                <span class="sub text-label text-uppercase fw-6">Nền tảng kinh nghiệm</span>
                            </div>
                            <div class="dot"></div>
                            <p>Đội ngũ lãnh đạo đảm nhiệm các vị trí cấp cao tại nhiều tập đoàn bất động sản lớn tại Việt Nam.</p>
                        </div>
                        <div class="time-item">
                            <div class="heading">
                                <h3 class="mb_8">2021</h3>
                                <span class="sub text-label text-uppercase fw-6">Khởi tạo thương hiệu</span>
                            </div>
                            <div class="dot"></div>
                            <p>Hình thành hệ sinh thái và chuẩn bị nền tảng vận hành cho giai đoạn tăng trưởng mới.</p>
                        </div>
                        <div class="time-item">
                            <div class="heading">
                                <h3 class="mb_8">2022</h3>
                                <span class="sub text-label text-uppercase fw-6">Ra mắt INDOCHINE</span>
                            </div>
                            <div class="dot"></div>
                            <p>Gia nhập thị trường trong giai đoạn khó khăn và ghi nhận nhiều giao dịch thành công, góp phần kích hoạt thanh khoản.</p>
                        </div>
                        <div class="time-item">
                            <div class="heading">
                                <h3 class="mb_8">2023 - Nay</h3>
                                <span class="sub text-label text-uppercase fw-6">Mở rộng quy mô</span>
                            </div>
                            <div class="dot"></div>
                            <p>Đẩy mạnh dịch vụ bất động sản toàn diện, mở rộng hợp tác và từng bước kết nối khách hàng quốc tế tại Việt Nam.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-process tf-spacing-1">
            <div class="tf-container">
                <div class="row tabs-hover-wrap align-items-center">
                    <div class="col-lg-5">
                        <div class="heading-section mb_48">
                            <span class="sub text-uppercase fw-6 text_secondary-color-2">Mục tiêu chiến lược</span>
                            <h3>Định hướng 5 năm</h3>
                        </div>
                        <div class="process-item item active" data-tab="tab1">
                            <span class="line"></span>
                            <div class="content">
                                <h5 class="title mb_8">Mũi nhọn đầu tư, kinh doanh và phân phối</h5>
                                <p>Trở thành đơn vị hàng đầu tại Việt Nam trong các lĩnh vực đầu tư, kinh doanh, phân phối và cho thuê bất động sản.</p>
                            </div>
                        </div>
                        <div class="process-item item" data-tab="tab2">
                            <span class="line"></span>
                            <div class="content">
                                <h5 class="title mb_8">Chuẩn hóa dịch vụ chuyên sâu</h5>
                                <p>Tiếp tục hoàn thiện hệ thống dịch vụ để tạo ra giải pháp phù hợp và giá trị thực cho từng nhóm khách hàng.</p>
                            </div>
                        </div>
                        <div class="process-item item" data-tab="tab3">
                            <span class="line"></span>
                            <div class="content">
                                <h5 class="title mb_8">Mở rộng thị trường quốc tế</h5>
                                <p>Lấy mảng dịch vụ làm trọng tâm để từng bước đưa thương hiệu tiếp cận khách hàng quốc tế có nhu cầu an cư và đầu tư tại Việt Nam.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="tab-content-wrap">
                            <div id="tab1" class="tab-content">
                                <div class="img-style">
                                    <img loading="lazy" decoding="async" src="images/section/process-1.jpg" width="690" height="518" alt="strategic">
                                </div>
                            </div>
                            <div id="tab2" class="tab-content">
                                <div class="img-style">
                                    <img loading="lazy" decoding="async" src="images/section/process-2.jpg" width="690" height="518" alt="service">
                                </div>
                            </div>
                            <div id="tab3" class="tab-content">
                                <div class="img-style">
                                    <img loading="lazy" decoding="async" src="images/section/process-3.jpg" width="690" height="518" alt="global">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3>Dịch vụ bất động sản toàn diện</h3>
                    <div class="wrap-counter">
                        <div class="counter-item style-default">
                            <div class="counter-number"><h1>01</h1></div>
                            <h6 class="text_secondary-color">Development</h6>
                        </div>
                        <div class="counter-item style-default">
                            <div class="counter-number"><h1>02</h1></div>
                            <h6 class="text_secondary-color">Consulting</h6>
                        </div>
                        <div class="counter-item style-default">
                            <div class="counter-number"><h1>03</h1></div>
                            <h6 class="text_secondary-color">Property</h6>
                        </div>
                        <div class="counter-item style-default">
                            <div class="counter-number"><h1>04</h1></div>
                            <h6 class="text_secondary-color">Hospitality & Global</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-why tf-spacing-1">
            <div class="tf-container">
                <div class="wrap-heading-section d-flex justify-content-between align-items-center mb_48">
                    <div class="heading-section ">
                        <span class="sub text-uppercase fw-6 text_secondary-color-2">One-Stop Real Estate Service</span>
                        <h3 class="text_white">Giải pháp đồng bộ<br>cho nhà đầu tư và khách hàng</h3>
                    </div>
                    <a href="{{ route('frontend.contact') }}" class="tf-btn btn-bg-white btn-px-32">
                        <span>Liên hệ tư vấn</span>
                        <span class="bg-effect"></span>
                    </a>
                </div>
                <div class="tf-grid-layout md-col-3">
                    <div class="tf-box-icon style-2 ">
                        <div class="icon mb_24"><i class="icon-Lifebuoy"></i></div>
                        <div class="content">
                            <h5 class="text_white mb_8">Development</h5>
                            <p class="text_secondary-color-2">Phát triển kinh doanh dự án bất động sản với định vị rõ ràng và chiến lược tăng trưởng bền vững.</p>
                        </div>
                    </div>
                    <div class="tf-box-icon style-2 ">
                        <div class="icon mb_24"><i class="icon-ClockCountdown"></i></div>
                        <div class="content">
                            <h5 class="text_white mb_8">Consulting & Property</h5>
                            <p class="text_secondary-color-2">Tư vấn chiến lược thương hiệu, marketing dự án; đồng thời phân phối và cho thuê sản phẩm hiệu quả.</p>
                        </div>
                    </div>
                    <div class="tf-box-icon style-2 ">
                        <div class="icon mb_24"><i class="icon-SketchLogo"></i></div>
                        <div class="content">
                            <h5 class="text_white mb_8">Hospitality & Global</h5>
                            <p class="text_secondary-color-2">Quản lý vận hành, khai thác tài sản và cung cấp dịch vụ trọn gói cho khách hàng quốc tế tại Việt Nam.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-testimonials tf-spacing-1">
            <div class="heading-section justify-content-center text-center mb_40">
                <span class="sub text-uppercase fw-6 text_secondary-color-2">Cam kết thương hiệu</span>
                <h3>Những giá trị chúng tôi theo đuổi</h3>
            </div>
            <div class="tf-container sw-layout">
                <div class="swiper" data-preview="2" data-tablet="2" data-mobile="1" data-mobile-sm="2" data-space-lg="30" data-space-md="24" data-space="15">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div>
                                    <p class="text_primary-color text-body-1 mb_23">“Lấy khách hàng làm trung tâm: ưu tiên giá trị thực, hiệu quả đầu tư và tính thanh khoản cho từng sản phẩm.”</p>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div>
                                    <p class="text_primary-color text-body-1 mb_23">“Làm nghề bằng năng lực, nhiệt huyết và sự am hiểu thị trường để trở thành cầu nối đáng tin cậy.”</p>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div>
                                    <p class="text_primary-color text-body-1 mb_23">“Phát triển bền vững dựa trên đạo đức kinh doanh, trách nhiệm xã hội và tinh thần tiên phong đổi mới.”</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-dots style-1 sw-pagination-layout justify-content-center d-flex mt_24"></div>
                </div>
            </div>
        </div>

        <div class="banner">
            <div class="parallaxie" style='background: url("images/section/banner.jpg")'>
                <div class="tf-container z-5">
                    <h2 class="text_white mb_20">Sẵn sàng đồng hành cùng bạn<br>trên hành trình an cư và đầu tư</h2>
                    <p class="text_white text-body-1">Liên hệ đội ngũ tư vấn để nhận giải pháp phù hợp nhất.</p>
                </div>
            </div>
        </div>

        
    </div>
@endsection
