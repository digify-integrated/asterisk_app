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
                        @slot('collapseId', 'system-action-permission-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'system-action-permission-table-column-dropdown')
                        @slot('dropdownButtonId', 'system-action-permission-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'system-action-permission-filter-collapse')
                @slot('resetFilterId', 'system-action-permission-reset-filters-btn')
                @slot('applyFilterId', 'system-action-permission-apply-filters-btn')

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_role_id">Role</label>
                    <select id="filter_role_id" name="filter_role_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Role" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_system_action_id">System Action</label>
                    <select id="filter_system_action_id" name="filter_system_action_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select System Action" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_access">Access</label>
                    <select id="filter_access" name="filter_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Access" data-allow-clear="true">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'system-action-permission-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'System Action Permission Details')
                @slot('formId', 'system_action_permission_form')
                @slot('size', 'lg')

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="role_id">Role</label>
                            <select id="role_id" name="role_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="system_action_id">System Action</label>
                            <select id="system_action_id" name="system_action_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>

                    <div class="separator separator-dashed"></div>

                    <div class="row gy-5">
                        <h6 class="fw-bold text-gray-800 mb-3 d-flex align-items-center">
                            <i class="ki-duotone ki-check fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Access Configuration
                        </h6>

                        <div class="col-12">
                            <label class="form-label required mb-2" for="access">Access</label>
                            <select id="access" name="access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="1">True</option>
                                <option value="0" selected>False</option>
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

