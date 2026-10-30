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

                <div class="d-flex align-items-center justify-content-start flex-wrap gap-2">
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

                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent  
                    
                    @component('partials.filter-button')
                        @slot('drawerId', 'employee-filter-drawer')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'employee-table-column-dropdown')
                        @slot('dropdownButtonId', 'employee-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>
        </div>
    </div>

	<div class="tab-content">
		<div id="employee-card-tab" class="tab-pane fade show active">
									
	    </div>

		<div id="employee-table-tab" class="tab-pane fade">
            <div class="card">
                <div class="card-body pt-1 pb-0 pe-0 ps-0">
                    @component('partials.index-table')
                        @slot('tableId', 'employee-table')
                    @endcomponent
                </div>
            </div>
	    </div>
	</div>

    @component('partials.filter-module')
        @slot('drawerId', 'employee-filter-drawer')
        @slot('resetFilterId', 'employee-reset-filters-btn')
        @slot('applyFilterId', 'employee-apply-filters-btn')

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_company_id">Company</label>
            <select id="filter_company_id" name="filter_company_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Company" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_employment_type_id">Employment Type</label>
            <select id="filter_employment_type_id" name="filter_employment_type_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Employment Type" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_department_id">Department</label>
            <select id="filter_department_id" name="filter_department_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Department" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_employment_status_id">Employment Status</label>
            <select id="filter_employment_status_id" name="filter_employment_status_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Employment Status" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_job_position_id">Job Position</label>
            <select id="filter_job_position_id" name="filter_job_position_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Job Position" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_manager_id">Manager</label>
            <select id="filter_manager_id" name="filter_manager_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Manager" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_onboard_date">Onboard Date</label>
            <div class="position-relative d-flex align-items-center">
                <i class="ki-duotone ki-calendar fs-6 position-absolute ms-3 text-gray-500"></i>
                <input type="text" id="filter_onboard_date" name="filter_onboard_date" class="form-control form-control-sm ps-10" placeholder="Pick date range" autocomplete="off"/>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_offboard_date">Offboard Date</label>
            <div class="position-relative d-flex align-items-center">
                <i class="ki-duotone ki-calendar fs-6 position-absolute ms-3 text-gray-500"></i>
                <input type="text" id="filter_offboard_date" name="filter_offboard_date" class="form-control form-control-sm ps-10" placeholder="Pick date range" autocomplete="off"/>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_gender_id">Gender</label>
            <select id="filter_gender_id" name="filter_gender_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Gender" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_marital_status_id">Marital Status</label>
            <select id="filter_marital_status_id" name="filter_marital_status_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Marital Status" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_religion_id">Religion</label>
            <select id="filter_religion_id" name="filter_religion_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Religion" data-allow-clear="true"></select>
        </div>

        <div class="col-12">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_blood_type_id">Blood Type</label>
            <select id="filter_blood_type_id" name="filter_blood_type_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Blood Type" data-allow-clear="true"></select>
        </div>
    @endcomponent
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
@endpush