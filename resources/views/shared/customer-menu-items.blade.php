@php
    $menu = $menu ?? customerMenuData();
    $variant = $variant ?? 'legacy';
    $stats = $menu['stats'] ?? [];
    $sections = $menu['sections'] ?? [];
@endphp

@if($variant === 'sneat')
    @foreach($sections as $section)
        @include('shared.sneat-menu-items', ['items' => $section['items']])
    @endforeach
@else
    @if(!empty($stats))
        @foreach($stats as $stat)
            <li class="navigation-header customer-details">
                <span>{{ $stat['label'] }}</span><br>
                <strong>{{ $stat['value'] }}</strong>
            </li>
        @endforeach
    @endif

    @foreach($sections as $section)
        <li class="block-header-color navigation-header">
            <span>{{ $section['label'] }}</span>
        </li>

        @foreach($section['items'] as $item)
            @if(!empty($item['modern_only']))
                @continue
            @endif
            @php
                $currentProductId = request()->integer('product');
                $itemProductId = (int) ($item['product_id'] ?? 0);
                $isShortcut = $itemProductId > 0;
                $isActive = $itemProductId > 0
                    ? false
                    : menuItemIsActive($item['active_paths'] ?? []);
                $isLogout = ($item['type'] ?? null) === 'logout';
                $target = $item['target'] ?? null;
            @endphp
            <li class="{{ $isActive ? 'active' : '' }} svg {{ $isShortcut ? 'menu-shortcut-item' : '' }}">
                <a
                    href="{{ $item['href'] }}"
                    @if($isShortcut) style="opacity:.78;" @endif
                    @if($target) target="{{ $target }}" @endif
                    @if($isLogout) onclick="event.preventDefault(); document.getElementById('logout-form').submit();" @endif
                >
                    @if(!empty($item['icon_html']))
                        {!! $item['icon_html'] !!}
                    @else
                        <i class="{{ menuIconClass($item['icon_key'] ?? 'circle', 'legacy') }}"></i>
                    @endif
                    <span class="menu-title"> &nbsp;{{ $item['label'] }}</span>
                    {{-- @if($isShortcut)
                        <small class="text-muted" style="margin-left:.4rem;font-size:.65rem;letter-spacing:.04em;text-transform:uppercase;">shortcut</small>
                    @endif --}}
                </a>
                @if($isLogout)
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            </li>
        @endforeach
    @endforeach
@endif
