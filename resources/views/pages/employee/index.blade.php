@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body py-5 px-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                <div class="d-flex align-items-center justify-content-start flex-wrap gap-2">
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
                
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'gender-filter-collapse')
                @slot('resetFilterId', 'gender-reset-filters-btn')
                @slot('applyFilterId', 'gender-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="d-flex flex-wrap flex-stack">
        <h3 class="fw-bold my-2">{{ $pageTitle }}</h3>

        <div class="d-flex flex-wrap my-1 mb-8">
            <ul class="nav nav-pills me-5">
                <li class="nav-item m-0">
                    <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary active me-3" data-bs-toggle="tab" href="#employee-card-tab">
                    <i class="ki-duotone ki-element-plus fs-1"></i></a>
                </li>

                <li class="nav-item m-0">
                    <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary" data-bs-toggle="tab" href="#employee-table-tab">
                    <i class="ki-duotone ki-row-horizontal fs-2"></i></a>
                </li>
            </ul>
        </div>
    </div>

	<div class="tab-content">
		<div id="employee-card-tab" class="tab-pane fade show active">
									
	    </div>

		<div id="employee-table-tab" class="tab-pane fade">
								
	    </div>
	</div>
@endsection

