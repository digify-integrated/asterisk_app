@php
    $hasChildren = !empty($item['children']);
    $isTopLevel = ($level === 0);

    $defaultIcon = 'ki-duotone ki-abstract-26';
    $iconClass = !empty($item['icon']) ? $item['icon'] : $defaultIcon;

    $currentMenuId = request()->route('navigationMenuId');

    /*
     * Check if this item itself is the current page.
     */
    $isActive = ((string) $currentMenuId === (string) $item['id']);

    /*
     * Recursively check all descendants.
     */
    $hasActiveChild = false;

    if ($hasChildren) {
        $checkActive = function ($children) use (&$checkActive, $currentMenuId) {
            foreach ($children as $child) {
                // Child itself is active
                if ((string) $child['id'] === (string) $currentMenuId) {
                    return true;
                }

                // Check deeper levels
                if (!empty($child['children'])) {
                    if ($checkActive($child['children'])) {
                        return true;
                    }
                }
            }

            return false;
        };

        $hasActiveChild = $checkActive($item['children']);
    }

    /*
     * A parent should be open if:
     * - it has an active child/grandchild/etc.
     */
    $isOpen = $hasActiveChild;
@endphp

@if($hasChildren)

    <div
        data-kt-menu-trigger="click"
        class="menu-item menu-accordion {{ $isOpen ? 'here show' : '' }}"
    >
        <span class="menu-link">
            @if($isTopLevel)
                <span class="menu-icon">
                    <i class="{{ $iconClass }} fs-2"></i>
                </span>
            @else
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
            @endif

            <span class="menu-title">
                {{ $item['name'] }}
            </span>

            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ $isOpen ? 'show' : '' }}">
            @foreach($item['children'] as $child)
                @include('partials.nav-item', [
                    'item' => $child,
                    'level' => $level + 1,
                    'nav_app_id' => $nav_app_id
                ])
            @endforeach
        </div>
    </div>

@else

    <div class="menu-item">
        <a
            class="menu-link {{ $isActive ? 'active' : '' }}"
            href="{{ $item['link'] }}"
        >
            @if($isTopLevel)
                <span class="menu-icon">
                    <i class="{{ $iconClass }} fs-2"></i>
                </span>
            @else
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
            @endif

            <span class="menu-title">
                {{ $item['name'] }}
            </span>
        </a>
    </div>

@endif