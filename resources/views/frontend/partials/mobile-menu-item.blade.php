@php
    $menuUrl = $menu->resolved_url ?? url($menu->slug ? '/' . ltrim($menu->slug, '/') : '/');
    $collapseId = 'mobile-menu-' . $menu->id;
@endphp

<li class="menu-item {{ $menu->children_tree->isNotEmpty() ? 'menu-item-has-children-mobile' : '' }}">
    @if ($menu->children_tree->isNotEmpty())
        <a href="#{{ $collapseId }}" class="item-menu-mobile collapsed" data-bs-toggle="collapse" aria-expanded="false" aria-controls="{{ $collapseId }}">
            {{ $menu->name }}
        </a>
        <div id="{{ $collapseId }}" class="collapse" data-bs-parent="#menu-mobile-menu">
            <ul class="sub-mobile">
                @foreach ($menu->children_tree as $child)
                    @include('frontend.partials.mobile-menu-item', ['menu' => $child])
                @endforeach
            </ul>
        </div>
    @else
        <a href="{{ $menuUrl }}" class="item-menu-mobile" @if($menu->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
            {{ $menu->name }}
        </a>
    @endif
</li>
