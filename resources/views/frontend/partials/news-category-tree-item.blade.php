@php
    $isActive = isset($currentCategory) && $currentCategory?->id === $category->id;
@endphp

<li>
    <div class="d-flex align-items-center justify-content-between text-body-default gap_12">
        <a href="{{ route('frontend.categories.show', $category->slug) }}" class="hover-line-text {{ $isActive ? 'text_primary-color fw-6' : '' }}">
            {{ $category->name }}
        </a>
        <div class="number">({{ $category->news_posts_count ?? 0 }})</div>
    </div>

    @if (($category->children_tree ?? collect())->isNotEmpty())
        <ul class="list-categories d-grid gap_8 mt_8 ms_16">
            @foreach ($category->children_tree as $child)
                @include('frontend.partials.news-category-tree-item', [
                    'category' => $child,
                    'currentCategory' => $currentCategory ?? null,
                ])
            @endforeach
        </ul>
    @endif
</li>
