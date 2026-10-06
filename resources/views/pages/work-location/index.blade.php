@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-7">
        <div class="card-body py-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-5">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>

                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">

                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent                   

                    @component('partials.filter-button')
                        @slot('collapseId', 'work-location-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'work-location-table-column-dropdown')
                        @slot('dropdownButtonId', 'work-location-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'work-location-filter-collapse')
                @slot('resetFilterId', 'work-location-reset-filters-btn')
                @slot('applyFilterId', 'work-location-apply-filters-btn')

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_location_type">Location Type</label>
                    <select id="filter_location_type" name="filter_location_type[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Entity Type" data-allow-clear="true">
                        <option value="Home">Home</option>
                        <option value="Office">Office</option>
                        <option value="Warehouse">Warehouse</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_city_id">City</label>
                    <select id="filter_city_id" name="filter_city_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select City" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_state_id">State</label>
                    <select id="filter_state_id" name="filter_state_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select State" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_country_id">Country</label>
                    <select id="filter_country_id" name="filter_country_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Country" data-allow-clear="true"></select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'work-location-table')
            @endcomponent
        </div>

        @component('partials.form-modal')
            @slot('formTitle', 'Work Location Details')
            @slot('formId', 'work_location_form')
            @slot('size', 'lg')
                
            <input type="hidden" id="work_location_id" name="work_location_id" />

            <div class="d-flex flex-column gap-7">
                <div class="d-flex flex-column gap-5">
                    <div class="row g-5">

                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="location_type">Location Type</label>
                            <select id="location_type" name="location_type" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="Select location type">
                                <option value=""></option>
                                <option value="Home">Home</option>
                                <option value="Office">Office</option>
                                <option value="Warehouse">Warehouse</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="address-section-container" class="d-flex flex-column gap-5">
                    <div class="separator separator-dashed my-2"></div>

                    <div class="d-flex flex-column gap-5">
                        <h6 class="fw-bold text-gray-800 m-0 d-flex align-items-center">
                            <i class="ki-duotone ki-geolocation fs-4 me-2 text-primary"></i>
                            Work Location Address
                        </h6>

                        <div class="row g-5">
                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="street_1">Street 1</label>
                                <input type="text" class="form-control form-control-sm" id="street_1" name="street_1" placeholder="Enter street 1" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="street_2">Street 2</label>
                                <input type="text" class="form-control form-control-sm" id="street_2" name="street_2" placeholder="Enter street 2" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="barangay">Barangay</label>
                                <input type="text" class="form-control form-control-sm" id="barangay" name="barangay" placeholder="Enter barangay" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="city_id">City</label>
                                <select id="city_id" name="city_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-placeholder="Select city"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
@endpush

