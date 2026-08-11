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
                        @slot('collapseId', 'navigation-menu-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'navigation-menu-table-column-dropdown')
                        @slot('dropdownButtonId', 'navigation-menu-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'navigation-menu-filter-collapse')
                @slot('resetFilterId', 'navigation-menu-reset-filters-btn')
                @slot('applyFilterId', 'navigation-menu-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'navigation-menu-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Navigation Menu Details')
                @slot('formId', 'upload_setting_form')
                @slot('size', 'lg')
                
                <input type="hidden" id="upload_setting_id" name="upload_setting_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="app_id">App</label>
                            <select id="app_id" name="app_id[]" multiple class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="page_type">Page Type</label>
                            <select id="page_type" name="page_type" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select page type">
                                <option value="menu">Menu</option>
                                <option value="single_page">Single Page</option>
                                <option value="multi_page">Multi Page</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="icon">Icon</label>
                            <select id="icon" name="icon" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-placeholder="Select icon">
                                @include('partials.icon-options')
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="parent_id">Parent</label>
                            <select id="parent_id" name="parent_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false">
                                <option value="">--</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="order_sequence">Order Sequence</label>
                            <input type="number" class="form-control form-control-sm" id="order_sequence" name="order_sequence" placeholder="0" min="0" max="100">
                        </div>
                    </div>

                    <div class="separator separator-dashed"></div>

                    <div class="row">
                        <h6 class="fw-bold text-gray-800 mb-3 d-flex align-items-center">
                            <i class="ki-duotone ki-element-plus fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Index Page Configuration
                        </h6>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="index_view_file">View File</label>
                            <input type="text" class="form-control form-control-sm" id="index_view_file" name="index_view_file" placeholder="Enter view file" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="index_js_file">JS File</label>
                            <input type="text" class="form-control form-control-sm" id="index_js_file" name="index_js_file" placeholder="Enter JS file" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <h6 class="fw-bold text-gray-800 mb-3 d-flex align-items-center">
                            <i class="ki-duotone ki-setting-2 fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Manage Page Configuration
                        </h6>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="manage_view_file">View File</label>
                            <input type="text" class="form-control form-control-sm" id="manage_view_file" name="manage_view_file" placeholder="Enter view file" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="manage_js_file">JS File</label>
                            <input type="text" class="form-control form-control-sm" id="manage_js_file" name="manage_js_file" placeholder="Enter JS file" maxlength="100" autocomplete="off">
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

