<nav class="main-menu">
    <ul class="navigation">
        <li class="text-menu {{ request()->routeIs('frontend.home') ? 'current-menu' : '' }}">
            <a href="{{ route('frontend.home') }}" class="toggle splitting">
                <span class="text" data-splitting>Trang chủ</span>
                <span class="text" data-splitting>Trang chủ</span>
            </a>
        </li>
        @foreach ($menuTree ?? collect() as $menu)
            @include('frontend.partials.desktop-menu-item', ['menu' => $menu])
        @endforeach
    </ul>
</nav>
