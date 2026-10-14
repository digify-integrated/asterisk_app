@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-5 px-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>
                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent  
                    
                    @component('partials.filter-button')
                        @slot('collapseId', 'gender-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'gender-table-column-dropdown')
                        @slot('dropdownButtonId', 'gender-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'gender-filter-collapse')
                @slot('resetFilterId', 'gender-reset-filters-btn')
                @slot('applyFilterId', 'gender-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <!-- Main Header Container -->
<div class="d-flex flex-wrap align-items-center justify-content-between pb-4 mb-6 border-bottom border-gray-200">
    
    <!-- Title Section with Vercel Minimalist Styling -->
    <div class="d-flex align-items-center gap-3 my-1">
        <h3 class="fs-4 fw-bold text-gray-900 mb-0 ls-tight">{{ $pageTitle }}</h3>
    </div>

    <!-- Navigation & View Controls -->
    <div class="d-flex flex-wrap align-items-center gap-3 my-1">
        
        <!-- Vercel-style Tab Navigation -->
        <ul class="nav nav-tabs nav-line-tabs border-0 fs-6 fw-medium gap-1 mb-0" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-gray-600 text-active-gray-900 border-0 pb-2 pt-2 px-3 rounded-2 position-relative active" 
                   data-bs-toggle="tab" 
                   href="#kt_customer_view_overview_tab" 
                   role="tab">
                    Overview
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-gray-600 text-active-gray-900 border-0 pb-2 pt-2 px-3 rounded-2 position-relative" 
                   data-bs-toggle="tab" 
                   href="#kt_customer_view_overview_events_and_logs_tab" 
                   role="tab">
                    Events &amp; Logs
                </a>
            </li>
        </ul>

        <!-- Vertical Separator -->
        <div class="vr h-20px text-gray-300 opacity-50 mx-1"></div>

        <!-- Vercel Segmented View Toggle Switch (Table / Card) -->
        <div class="p-1 bg-gray-100 rounded-2 border border-gray-200 d-inline-flex align-items-center" role="group" aria-label="View Mode Toggle">
            <button type="button" 
                    class="btn btn-sm btn-icon h-28px w-28px rounded-1 bg-white shadow-xs text-gray-900 border-0 active-view-btn" 
                    data-view="table" 
                    title="Table View">
                <!-- List/Table Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
            </button>
            <button type="button" 
                    class="btn btn-sm btn-icon h-28px w-28px rounded-1 bg-transparent text-gray-500 border-0 hover-text-gray-900" 
                    data-view="card" 
                    title="Card View">
                <!-- Grid/Card Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </button>
        </div>

    </div>
</div>
@endsection

