<li>
    <a href="{{ route('frontend.products.category', $category->slug) }}" class="{{ isset($currentCategory) && $currentCategory->id === $category->id ? 'text_primary-color fw-7' : '' }}">
        {{ $category->name }}
    </a>
    @if ($category->children_tree->isNotEmpty())
        <ul class="mt_8 ms_16 d-grid gap_8">
            @foreach ($category->children_tree as $child)
                @include('frontend.partials.category-tree-item', ['category' => $child, 'currentCategory' => $currentCategory ?? null])
            @endforeach
        </ul>
    @endif
</li>
