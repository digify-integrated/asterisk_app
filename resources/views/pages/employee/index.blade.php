@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body py-5 px-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">                
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>

                <div class="d-flex flex-wrap">
                    <ul class="nav nav-pills me-5">
                        <li class="nav-item m-0">
                            <a class="btn btn-sm btn-icon btn-light btn-color-muted active me-3" data-bs-toggle="tab" href="#employee-card-tab">
                            <i class="ki-duotone ki-element-plus fs-1"></i></a>
                        </li>

                        <li class="nav-item m-0">
                            <a class="btn btn-sm btn-icon btn-light btn-color-muted" data-bs-toggle="tab" href="#employee-table-tab">
                            <i class="ki-duotone ki-row-horizontal fs-2"></i></a>
                        </li>
                    </ul>
                </div>

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
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'gender-filter-collapse')
                @slot('resetFilterId', 'gender-reset-filters-btn')
                @slot('applyFilterId', 'gender-apply-filters-btn')
            @endcomponent
        </div>
    </div>

	<div class="tab-content">
		<div id="employee-card-tab" class="tab-pane fade show active">
									
	    </div>

		<div id="employee-table-tab" class="tab-pane fade">
            <div class="card">
                <div class="card-body pt-1 pb-0 pe-0 ps-0">
                    @component('partials.index-table')
                        @slot('tableId', 'marital-status-table')
                    @endcomponent
                </div>

                @component('partials.form-modal')
                    @slot('formTitle', 'Marital Status Details')
                    @slot('formId', 'marital_status_form')
                    @slot('size', 'md')

                    <input type="hidden" id="marital_status_id" name="marital_status_id" />

                    <div class="d-flex flex-column gap-7">
                        <div class="row g-5">
                            <div class="col-12">
                                <label class="form-label required mb-2" for="name">Name</label>
                                <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
	    </div>
	</div>
@endsection

