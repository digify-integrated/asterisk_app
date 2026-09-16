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
                        @slot('collapseId', 'company-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'company-table-column-dropdown')
                        @slot('dropdownButtonId', 'company-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'company-filter-collapse')
                @slot('resetFilterId', 'company-reset-filters-btn')
                @slot('applyFilterId', 'company-apply-filters-btn')

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_status">Status</label>
                    <select id="filter_status" name="filter_status[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Status" data-allow-clear="true">
                        <option value="Inactive">Inactive</option>
                        <option value="Active">Active</option>
                    </select>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'company-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Company Details')
                @slot('formId', 'company_form')
                @slot('size', 'lg')
                
                <input type="hidden" id="company_id" name="company_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-2" for="logo">Logo</label>
                            <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                        </div>
                    </div>

                    <div class="row gy-5">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="legal_name">Legal Name</label>
                            <input type="text" class="form-control form-control-sm" id="legal_name" name="legal_name" placeholder="Enter legal name" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="trade_name">Trade Name</label>
                            <input type="text" class="form-control form-control-sm" id="trade_name" name="trade_name" placeholder="Enter trade name" maxlength="200" autocomplete="off">
                        </div>
                    </div>

                    <div class="row gy-5">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="tin">TIN</label>
                            <input type="text" class="form-control form-control-sm" id="tin" name="tin" placeholder="Enter tin" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="branch_code">Branch Code</label>
                            <input type="text" class="form-control form-control-sm" id="branch_code" name="branch_code" placeholder="Enter branch code" maxlength="100" autocomplete="off">
                        </div>
                    </div>

                    <div class="row gy-5">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="rdo_code">RDO Code</label>
                            <input type="text" class="form-control form-control-sm" id="rdo_code" name="rdo_code" placeholder="Enter RDO code" maxlength="100" autocomplete="off">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="entity_type">Entity Type</label>
                            <select id="entity_type" name="entity_type" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select entity type">
                                <option value="Corporation">Corporation</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Sole Proprietorship">Sole Proprietorship</option>
                                <option value="Cooperative">Cooperative</option>
                                <option value="One Person Corporation (OPC)">One Person Corporation (OPC)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row gy-5">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="sec_dti_registration_no">SEC/DTI Registration No.</label>
                            <input type="text" class="form-control form-control-sm" id="sec_dti_registration_no" name="sec_dti_registration_no" placeholder="Enter SEC/DTI registration no." maxlength="100" autocomplete="off">
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

