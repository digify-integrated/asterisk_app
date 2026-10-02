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
                        @slot('collapseId', 'department-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'department-table-column-dropdown')
                        @slot('dropdownButtonId', 'department-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'department-filter-collapse')
                @slot('resetFilterId', 'department-reset-filters-btn')
                @slot('applyFilterId', 'department-apply-filters-btn')

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_parent_id">Parent</label>
                    <select id="filter_parent_id" name="filter_parent_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Parent" data-allow-clear="true"></select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'department-table')
            @endcomponent
        </div>

        @component('partials.form-modal')
            @slot('formTitle', 'Department Details')
            @slot('formId', 'department_form')
            @slot('size', 'md')

            <input type="hidden" id="department_id" name="department_id" />

            <div class="d-flex flex-column gap-7">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label required mb-2" for="name">Name</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="form-label mb-2" for="parent_id">Parent</label>
                        <select id="parent_id" name="parent_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-placeholder="Select parent"></select>
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

