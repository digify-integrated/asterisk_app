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
                        @slot('collapseId', 'page-permission-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'page-permission-table-column-dropdown')
                        @slot('dropdownButtonId', 'page-permission-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'page-permission-filter-collapse')
                @slot('resetFilterId', 'page-permission-reset-filters-btn')
                @slot('applyFilterId', 'page-permission-apply-filters-btn')

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_role_id">Role</label>
                    <select id="filter_role_id" name="filter_role_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Role" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_navigation_menu_id">Page</label>
                    <select id="filter_navigation_menu_id" name="filter_navigation_menu_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Page" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_read_access">Read Access</label>
                    <select id="filter_read_access" name="filter_read_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Read Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_write_access">Write Access</label>
                    <select id="filter_write_access" name="filter_write_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Write Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_create_access">Create Access</label>
                    <select id="filter_create_access" name="filter_create_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Create Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_delete_access">Delete Access</label>
                    <select id="filter_delete_access" name="filter_delete_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Delete Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_export_access">Export Access</label>
                    <select id="filter_export_access" name="filter_export_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Export Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_logs_access">Logs Access</label>
                    <select id="filter_logs_access" name="filter_logs_access[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Logs Access" data-allow-clear="true">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'page-permission-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Page Permission Details')
                @slot('formId', 'page_permission_form')
                @slot('size', 'md')

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="role_id">Role</label>
                            <select id="role_id" name="role_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="navigation_menu_id">Page</label>
                            <select id="navigation_menu_id" name="navigation_menu_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>

                    <div class="separator separator-dashed"></div>

                    <div class="row">
                        <h6 class="fw-bold text-gray-800 mb-3 d-flex align-items-center">
                            <i class="ki-duotone ki-check fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Access Configuration
                        </h6>

                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="read_access">Read Access</label>
                            <select id="read_access" name="read_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="write_access">Write Access</label>
                            <select id="write_access" name="write_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="create_access">Create Access</label>
                            <select id="create_access" name="create_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="delete_access">Delete Access</label>
                            <select id="delete_access" name="delete_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="export_access">Export Access</label>
                            <select id="export_access" name="export_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="logs_access">Logs Access</label>
                            <select id="logs_access" name="logs_access" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select access">
                                <option value="true">True</option>
                                <option value="false" selected>False</option>
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

