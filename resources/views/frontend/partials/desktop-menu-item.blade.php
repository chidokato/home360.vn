@php
    $menuUrl = $menu->resolved_url ?? url($menu->slug ? '/' . ltrim($menu->slug, '/') : '/');
    $isActive = url()->current() === $menuUrl;
    $level = $level ?? 0;
@endphp

<li class="{{ $menu->children_tree->isNotEmpty() ? 'has-child' : '' }} {{ $level === 0 ? 'text-menu' : '' }} {{ $isActive ? 'current-menu' : '' }}">
    @if ($level === 0)
        <a href="{{ $menuUrl }}" class="toggle splitting" @if($menu->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
            <span class="text" data-splitting>{{ $menu->name }}</span>
            <span class="text" data-splitting>{{ $menu->name }}</span>
        </a>
    @else
        <a href="{{ $menuUrl }}" @if($menu->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
            {{ $menu->name }}
        </a>
    @endif

    @if ($menu->children_tree->isNotEmpty())
        <ul class="submenu">
            @foreach ($menu->children_tree as $child)
                @include('frontend.partials.desktop-menu-item', ['menu' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
