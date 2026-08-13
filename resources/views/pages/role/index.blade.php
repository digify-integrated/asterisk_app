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
                        @slot('collapseId', 'role-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'role-table-column-dropdown')
                        @slot('dropdownButtonId', 'role-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'role-filter-collapse')
                @slot('resetFilterId', 'role-reset-filters-btn')
                @slot('applyFilterId', 'role-apply-filters-btn')
                
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_user_id">User Account</label>
                    <select id="filter_user_id" name="filter_user_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select User Account" data-allow-clear="true"></select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'role-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Role Details')
                @slot('formId', 'role_form')
                @slot('size', 'lg')
                
                <input type="hidden" id="role_id" name="role_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-2" for="user_id">User Accounts</label>
                            <select id="user_id" name="user_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="description">Description</label>
                            <textarea class="form-control form-control-sm" id="description" name="description" rows="3" placeholder="Briefly describe the role..." maxlength="500"></textarea>
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

