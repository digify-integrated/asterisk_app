@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-4">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>
                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent  
                    
                    @component('partials.filter-button')
                        @slot('collapseId', 'city-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'city-table-column-dropdown')
                        @slot('dropdownButtonId', 'city-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'city-filter-collapse')
                @slot('resetFilterId', 'city-reset-filters-btn')
                @slot('applyFilterId', 'city-apply-filters-btn')
                
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_city_id">Country</label>
                    <select id="filter_city_id" name="filter_city_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Country" data-allow-clear="true"></select>
                </div>
                
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_state_id">State</label>
                    <select id="filter_state_id" name="filter_state_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select State" data-allow-clear="true"></select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'city-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'City Details')
                @slot('formId', 'city_form')
                @slot('size', 'md')

                <input type="hidden" id="city_id" name="city_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="state_id">State</label>
                            <select id="state_id" name="state_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false">
                                <option value="">--</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endcomponent
        @endif
    </div>

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>

    @if (!empty($jsFile))
        <script type="module" src="{{ asset('assets/js/pages/' . $jsFile . '.js') }}"></script>
    @endif
@endpush

