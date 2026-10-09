@php
    $isManageRoute = request()->routeIs('apps.manage');
    $isImportRoute = request()->routeIs('apps.import');

    $routeSuffix = $isManageRoute
        ? 'Manage'
        : ($isImportRoute ? 'Import' : '');

    $isManageOrImport = $isManageRoute || $isImportRoute;
@endphp

<ol class="breadcrumb breadcrumb-dot text-muted fs-8 fw-semibold">
    <li class="breadcrumb-item">
        <a href="{{ route('apps.main') }}" class="text-muted text-hover-primary">
            Home {{ $routeName ?? '' }}
        </a>
    </li>

    @foreach(($bc_items ?? []) as $item)
        @php
            $isNavCrumb = !is_null($item['id'] ?? null);
            $isLast = $loop->last;

            $isClickable = $isNavCrumb
                && ($item['has_route'] ?? false)
                && (!$isLast || $isManageOrImport);
        @endphp

        <li class="breadcrumb-item {{ $isLast && !$isManageOrImport ? 'text-muted' : '' }}">
            @if($isClickable)
                <a
                    href="{{ route('apps.base', [
                        'appId' => $bc_app_id,
                        'navigationMenuId' => $item['id']
                    ]) }}"
                    class="text-muted text-hover-primary"
                >
                    {{ $item['label'] }}
                </a>
            @else
                {{ $item['label'] }}
            @endif
        </li>
    @endforeach

    @if($routeSuffix !== '')
        <li class="breadcrumb-item text-muted">
            {{ $routeSuffix }}
        </li>
    @endif
</ol>