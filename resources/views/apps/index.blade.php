@extends('layouts.app')

@section('content')
<div class="container-fluid px-8 py-10">
    <div class="mb-12">
        <h1 class="display-5 fw-bold text-dark mb-3">Applications</h1>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted fs-6">Choose an application to continue.</span>
            <span class="text-muted">•</span>
            <span class="fw-semibold text-dark">{{ $apps->count() }} Available</span>
        </div>
    </div>

    <div class="row g-7">
        @forelse($apps as $app)
            @php
                // Group all menus by their parent_id for rapid hierarchical lookup
                $menusByParent = $app->navigationMenus->groupBy(function ($item) {
                    return $item->parent_id ?? 0;
                });

                // Helper function to recursively find the first actionable leaf page inside a parent menu tree
                $findFirstLeafPage = function ($parentId) use (&$findFirstLeafPage, $menusByParent) {
                    $children = $menusByParent->get($parentId, collect());

                    foreach ($children as $child) {
                        // If it's a direct page, return it immediately
                        if ($child->page_type !== 'menu') {
                            return $child;
                        }

                        // If it's a folder/menu container, recursively search inside its children
                        $leaf = $findFirstLeafPage($child->id);
                        if ($leaf) {
                            return $leaf;
                        }
                    }

                    return null;
                };

                // Find the absolute first executable menu starting from root parents (parent_id = 0)
                $firstMenu = $findFirstLeafPage(0);

                $defaultLink = $firstMenu
                    ? route('apps.base', [
                        'appId' => $app->id,
                        'navigationMenuId' => $firstMenu->id
                    ])
                    : '#';

                $defaultLogo = asset('assets/media/default/app-logo.png');
                $logoPath = trim((string) $app->logo);

                $logoUrl = ($logoPath !== '' && Storage::disk('public')->exists($logoPath))
                    ? Storage::url($logoPath)
                    : $defaultLogo;
            @endphp

            <div class="col-sm-6 col-lg-4 col-xl-3">
                <a href="{{ $defaultLink }}" class="text-decoration-none">
                    <div class="card app-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="app-icon">
                                    <img src="{{ $logoUrl }}" alt="{{ $app->name }}">
                                </div>

                                <i class="ki-outline ki-arrow-up-right fs-3 text-muted"></i>
                            </div>

                            <div class="mt-8">
                                <h3 class="fw-bold fs-4 text-dark mb-2">{{ $app->name }}</h3>

                                <p class="text-muted fs-7 mb-0">{{ $app->description }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        @empty

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body py-20 text-center">
                        <img src="{{ asset('assets/media/default/app-logo.png') }}" width="72" class="mb-6">
                        <h3 class="fw-bold mb-3">No Applications</h3>
                        <p class="text-muted mb-0">Your administrator hasn't assigned any applications yet.</p>
                    </div>
                </div>
            </div>

        @endforelse

    </div>
</div>
@endsection