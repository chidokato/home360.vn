@extends('frontend.layouts.app')

@section('title', $pageTitle ?? 'Tin tuc')
@section('meta_description', $pageDescription ?? 'Danh sach tin tuc')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/news-list.css') }}">
@endpush

@php
    $formatDate = static fn ($value) => optional($value ?? null)?->format('d/m/Y');
@endphp

@section('content')
<div class="main-content">
    <div class="tf-container tf-spacing-1 blog-list">
        <div class="row">
            <div class="col-lg-8">
                <div class="heading-section mb_30">
                    <span class="text_primary-color text-button-small text-uppercase">Tin tuc</span>
                    <h3 class="mt_8 mb_8">{{ $currentCategory?->name ?? 'Tin tuc moi nhat' }}</h3>
                    <p class="text-body-default">
                        {{ $currentCategory?->description ?: 'Tong hop cac bai viet tin tuc moi nhat tu BDSVanPhuc.' }}
                    </p>
                </div>

                <div class="wrap-blog style-list">
                    @forelse ($posts as $post)
                        @php
                            $postUrl = $post->frontend_url;
                            $postImage = $post->image ? asset($post->image) : asset('images/blog/blog-item-list-1.jpg');
                            $displayDate = optional($post->published_at ?: $post->created_at)->format('d/m/Y');
                            $description = $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 180);
                        @endphp
                        <div class="blog-article-item style-list hover-image-translate">
                            <div class="article-thumb image-wrap">
                                <img loading="lazy" decoding="async" src="{{ $postImage }}" width="850" height="478" alt="{{ $post->title }}">
                                @if ($post->category)
                                    <a href="{{ route('frontend.categories.show', $post->category->slug) }}" class="tag text-label text text_primary-color text-uppercase">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                                <a href="{{ $postUrl }}" class="overlay-link"></a>
                            </div>
                            <div class="article-content">
                                <div class="meta-post d-flex align-items-center mb_12">
                                    <div class="item text_secondary-color text-caption-1">{{ $displayDate }}</div>
                                </div>
                                <h5 class="title mb_12">
                                    <a href="{{ $postUrl }}" class="line-clamp-2 link">{{ $post->title }}</a>
                                </h5>
                                <p class="description text-body-default mb_20 line-clamp-3">{{ $description }}</p>
                                <a href="{{ $postUrl }}" class="hover-underline-link text-button text_primary-color">
                                    Xem chi tiet
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="sidebar-item">
                            <h5 class="mb_12">Chua co bai viet</h5>
                            <p class="text-body-default mb-0">Hien tai chua co bai tin tuc nao dang hien thi.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-4">
                <div class="tf-sidebar sticky-top news-list-sidebar">
                    <div class="sidebar-item sidebar-categories">
                        <h5 class="sidebar-title mb_17">Danh muc tin tuc</h5>
                        <ul class="list-categories d-grid gap_8">
                            <li class="d-flex align-items-center justify-content-between text-body-default">
                                <a href="{{ route('frontend.news.index') }}" class="hover-line-text {{ $currentCategory ? '' : 'text_primary-color fw-6' }}">
                                    Tat ca tin tuc
                                </a>
                                <div class="number">({{ $categories->sum('news_posts_count') }})</div>
                            </li>
                            @foreach ($categories as $category)
                                @include('frontend.partials.news-category-tree-item', [
                                    'category' => $category,
                                    'currentCategory' => $currentCategory ?? null,
                                ])
                            @endforeach
                        </ul>
                    </div>

                    @if (($recentPosts ?? collect())->isNotEmpty())
                        <div class="sidebar-item sidebar-recent-post">
                            <h5 class="sidebar-title mb_17">Bai viet moi</h5>
                            <ul>
                                @foreach ($recentPosts as $recentPost)
                                    @php
                                        $recentUrl = $recentPost->frontend_url;
                                        $recentImage = $recentPost->image ? asset($recentPost->image) : asset('images/blog/recent-post-1.jpg');
                                        $recentDate = optional($recentPost->published_at ?: $recentPost->created_at)->format('d/m/Y');
                                    @endphp
                                    <li class="recent-post hover-image-rotate">
                                        <a href="{{ $recentUrl }}" class="img-style">
                                            <img src="{{ $recentImage }}" width="100" height="100" alt="{{ $recentPost->title }}">
                                        </a>
                                        <div class="content">
                                            <div class="meta-post d-flex align-items-center mb_7">
                                                <div class="item text_secondary-color text-caption-2">{{ $recentDate }}</div>
                                            </div>
                                            <div class="text-title title text_primary-color fw-6">
                                                <a href="{{ $recentUrl }}" class="link line-clamp-2">{{ $recentPost->title }}</a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
