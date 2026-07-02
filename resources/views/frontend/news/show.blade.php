@extends('frontend.layouts.app')

@section('title', $pageTitle ?? $post->title)
@section('meta_description', $pageDescription ?? ($post->summary ?: 'Chi tiet tin tuc'))

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/news-detail.css') }}">
@endpush

@section('content')
@php
    $heroImage = $post->image ? asset($post->image) : asset('images/blog/blog-item-1.jpg');
    $displayDate = optional($post->published_at ?: $post->created_at)->format('d/m/Y');
    $currentUrl = request()->fullUrl();
    $shareTitle = $post->title;
@endphp

<div class="thumbs-main-post">
    <div class="thumbs">
        <img src="{{ $heroImage }}" alt="{{ $post->title }}">
    </div>
</div>

<div class="main-content">
    <div class="blog-post">
        <div class="tf-container tf-spacing-1">
            <div class="row">
                <div class="col-lg-8">
                    <div class="details-post">
                        <div class="heading-title mb_24">
                            @if ($post->category)
                                <div class="tag-heading text-button-small text_primary-color">
                                    {{ strtoupper($post->category->name) }}
                                </div>
                            @endif

                            <h1>{{ $post->title }}</h1>

                            <div class="meta-post d-flex align-items-center">
                                <div class="item text_primary-color text-title fw-6 d-flex align-items-center gap_8">
                                    <i class="icon-CalendarBlank"></i> {{ $displayDate }}
                                </div>
                            </div>
                        </div>

                        @if ($post->summary)
                            <p class="passive text-body-2">{{ $post->summary }}</p>
                        @endif

                        <div class="passive text-body-2 news-detail-body">
                            {!! $post->content !!}
                        </div>

                        <div class="tag-share d-flex justify-content-between">
                            <div class="tag d-flex align-items-center gap_12">
                                <span class="text-button fw-7 text_primary-color">Tag:</span>
                                <ul class="tags-list">
                                    @if ($post->category)
                                        <li>
                                            <a href="{{ route('frontend.categories.show', $post->category->slug) }}" class="tags-item text-caption-1">
                                                {{ $post->category->name }}
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('frontend.news.index') }}" class="tags-item text-caption-1">Tin tuc</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="share d-flex align-items-center gap_16">
                                <span class="text-button fw-7 text_primary-color">Share This Post:</span>
                                <ul class="tf-social d-flex gap_24">
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}" target="_blank" rel="noopener noreferrer" class="icon-FacebookLogo"></a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($currentUrl) }}&text={{ urlencode($shareTitle) }}" target="_blank" rel="noopener noreferrer" class="icon-XLogo"></a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($currentUrl) }}" target="_blank" rel="noopener noreferrer" class="icon-InstagramLogo"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if ($relatedPosts->isNotEmpty())
                            <div class="tf-article-navigation">
                                @foreach ($relatedPosts->take(2) as $index => $relatedPost)
                                    <div class="item {{ $index === 1 ? 'next' : 'prev' }}">
                                        <a href="{{ $relatedPost->frontend_url }}" class="hover-underline-link text-button text_primary-color fw-7 mb_8">
                                            {{ $index === 0 ? 'Previous' : 'Next' }}
                                        </a>
                                        <h5>
                                            <a href="{{ $relatedPost->frontend_url }}" class="link line-clamp-2">{{ $relatedPost->title }}</a>
                                        </h5>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="tf-sidebar sticky-top news-post-sidebar">
                        @if (($categories ?? collect())->isNotEmpty())
                            <div class="sidebar-item sidebar-categories">
                                <h5 class="sidebar-title mb_17">Categories</h5>
                                <ul class="list-categories d-grid gap_8">
                                    <li class="d-flex align-items-center justify-content-between text-body-default">
                                        <a href="{{ route('frontend.news.index') }}" class="hover-line-text">Tat ca tin tuc</a>
                                        <div class="number">({{ $categories->sum('news_posts_count') }})</div>
                                    </li>
                                    @foreach ($categories as $category)
                                        @include('frontend.partials.news-category-tree-item', [
                                            'category' => $category,
                                            'currentCategory' => $post->category ?? null,
                                        ])
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (($recentPosts ?? collect())->isNotEmpty())
                            <div class="sidebar-item sidebar-recent-post">
                                <h5 class="sidebar-title mb_17">Recent Posts</h5>
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
                                                    @if ($recentPost->category)
                                                        <div class="item text_secondary-color text-caption-2">
                                                            <a href="{{ route('frontend.categories.show', $recentPost->category->slug) }}" class="link text_primary-color">
                                                                {{ $recentPost->category->name }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="item text_secondary-color text-caption-2">{{ $recentDate }}</div>
                                                </div>
                                                <div class="text-title title text_primary-color fw-6">
                                                    <a href="{{ $recentUrl }}" class="link">{{ $recentPost->title }}</a>
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
</div>
@endsection
