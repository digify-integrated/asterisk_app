@php
    $hasChildren = !empty($item['children']);
    $isTopLevel = ($level === 0);

    $defaultIcon = 'ki-duotone ki-abstract-26';
    $iconClass = !empty($item['icon']) ? $item['icon'] : $defaultIcon;

    // Fetch the active route menu ID to keep track of accordion/link active highlighting states
    $currentMenuId = request()->route('navigationMenuId');
    $isActive = ($currentMenuId == $item['id']);
    
    // Check if any recursive nested child is the currently active route page
    $hasActiveChild = false;
    if ($hasChildren) {
        $hasActiveChild = collect($item['children'])->pluck('id')->contains($currentMenuId);
    }
@endphp

@if($hasChildren)
    {{-- Dropdown Accordion Wrapper --}}
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $hasActiveChild ? 'here show' : '' }}">
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

            {{-- Swapped 'title' to 'name' --}}
            <span class="menu-title">{{ $item['name'] }}</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion {{ $hasActiveChild ? 'show' : '' }}">
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
    {{-- Clickable Executable Link Item --}}
    <div class="menu-item">
        {{-- Swapped hardcoded routing helper with the dynamic tree builder link property --}}
        <a class="menu-link {{ $isActive ? 'active' : '' }}" href="{{ $item['link'] }}">
            @if($isTopLevel)
                <span class="menu-icon">
                    <i class="{{ $iconClass }} fs-2"></i>
                </span>
            @else
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
            @endif

            {{-- Swapped 'title' to 'name' --}}
            <span class="menu-title">{{ $item['name'] }}</span>
        </a>
    </div>
@endif