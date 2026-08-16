@php
    $items = $items ?? [];
    $depth = isset($depth) && is_numeric($depth) ? (int) $depth : 0;
@endphp

@foreach($items as $item)
    @php
        $children = $item['children'] ?? [];
        $hasChildren = !empty($children);
        $currentProductId = request()->integer('product');
        $itemProductId = (int) ($item['product_id'] ?? 0);
        $isShortcut = $itemProductId > 0;
        $hasActiveChild = $itemProductId > 0 ? false : menuItemHasActiveChild($item);
        $isActive = $isShortcut
            ? false
            : menuItemIsActive($item['active_paths'] ?? []);
        $isActive = $isActive || $hasActiveChild;
        $isOpen = $hasChildren && $hasActiveChild;
        $isLogout = ($item['type'] ?? null) === 'logout';
        $target = $item['target'] ?? null;
        $href = $item['href'] ?? 'javascript:void(0);';
        $iconKey = $item['modern_icon_key'] ?? $item['icon_key'] ?? 'circle';
        $isSubItem = $depth > 0;
    @endphp

    <li class="menu-item {{ $isActive ? 'active' : '' }} {{ $isOpen ? 'open' : '' }} {{ $isShortcut ? 'menu-item--shortcut' : '' }}">
        <a
            href="{{ $hasChildren ? 'javascript:void(0);' : $href }}"
            class="menu-link {{ $hasChildren ? 'menu-toggle' : '' }} {{ $isSubItem ? 'menu-sub-link' : '' }} {{ $isShortcut ? 'menu-link--shortcut' : '' }}"
            @if($isShortcut) style="opacity:.78;" @endif
            @if($target) target="{{ $target }}" @endif
            @if($isLogout && !$hasChildren) onclick="event.preventDefault(); document.getElementById('logout-form').submit();" @endif
        >
            @if(!$isSubItem)
                <i class="{{ menuIconClass($iconKey, 'sneat') }}"></i>
            @endif
            @if($isSubItem)
                <span class="menu-sub-marker" aria-hidden="true">-</span>
            @endif
            <div data-i18n="{{ $item['label'] }}">{{ $item['label'] }}</div>
        </a>

        @if($hasChildren)
            <ul class="menu-sub">
                @include('shared.sneat-menu-items', ['items' => $children, 'depth' => $depth + 1])
            </ul>
        @endif

        @if($isLogout)
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif
    </li>
@endforeach
