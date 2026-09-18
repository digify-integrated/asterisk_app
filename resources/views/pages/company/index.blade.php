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
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_entity_type">Entity Type</label>
                    <select id="filter_entity_type" name="filter_entity_type[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Entity Type" data-allow-clear="true">
                        <option value="Corporation">Corporation</option>
                        <option value="Partnership">Partnership</option>
                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                        <option value="Cooperative">Cooperative</option>
                        <option value="One Person Corporation (OPC)">One Person Corporation (OPC)</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_vat_status">VAT Status</label>
                    <select id="filter_vat_status" name="filter_vat_status[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select VAT Status" data-allow-clear="true">
                        <option value="VAT-Registered">VAT-Registered</option>
                        <option value="Non-VAT">Non-VAT</option>
                        <option value="VAT-Exempt">VAT-Exempt</option>
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

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_currency_id">Currency</label>
                    <select id="filter_currency_id" name="filter_currency_id[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Currency" data-allow-clear="true"></select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_fiscal_year_start_month">Fiscal Year Start Month</label>
                    <select id="filter_fiscal_year_start_month" name="filter_fiscal_year_start_month[]" multiple class="form-select form-select-sm" data-control="select2" data-placeholder="Select Fiscal Year Start Month" data-allow-clear="true">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_date_registered">Date Registered</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="ki-outline ki-calendar fs-6 position-absolute ms-3 text-gray-500"></i>
                        <input type="text" id="filter_date_registered" name="filter_date_registered" class="form-control form-control-sm ps-10" placeholder="Pick date range" autocomplete="off"/>
                    </div>
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
                    <div class="d-flex flex-column gap-5">
                        <div class="row g-5">
                            <div class="col-12">
                                <label class="form-label mb-2" for="logo">Logo</label>
                                <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="legal_name">Legal Name</label>
                                <input type="text" class="form-control form-control-sm" id="legal_name" name="legal_name" placeholder="Enter legal name" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="trade_name">Trade Name</label>
                                <input type="text" class="form-control form-control-sm" id="trade_name" name="trade_name" placeholder="Enter trade name" maxlength="200" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="tin">TIN</label>
                                <input type="text" class="form-control form-control-sm" id="tin" name="tin" placeholder="Enter TIN" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="branch_code">Branch Code</label>
                                <input type="text" class="form-control form-control-sm" id="branch_code" name="branch_code" placeholder="Enter branch code" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="rdo_code">RDO Code</label>
                                <input type="text" class="form-control form-control-sm" id="rdo_code" name="rdo_code" placeholder="Enter RDO code" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="entity_type">Entity Type</label>
                                <select id="entity_type" name="entity_type" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="Select entity type">
                                    <option value=""></option>
                                    <option value="Corporation">Corporation</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Sole Proprietorship">Sole Proprietorship</option>
                                    <option value="Cooperative">Cooperative</option>
                                    <option value="One Person Corporation (OPC)">One Person Corporation (OPC)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="sec_dti_registration_no">SEC/DTI Registration No.</label>
                                <input type="text" class="form-control form-control-sm" id="sec_dti_registration_no" name="sec_dti_registration_no" placeholder="Enter SEC/DTI registration no." maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="date_registered">Date Registered</label>
                                <div class="position-relative d-flex align-items-center">
                                    <i class="ki-outline ki-calendar fs-6 position-absolute ms-3 text-gray-500"></i>
                                    <input type="text" id="date_registered" name="date_registered" class="form-control form-control-sm ps-10" placeholder="Pick date registered" autocomplete="off"/>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="psic_code">PSIC Code</label>
                                <input type="text" class="form-control form-control-sm" id="psic_code" name="psic_code" placeholder="Enter PSIC code" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="line_of_business">Line of Business</label>
                                <input type="text" class="form-control form-control-sm" id="line_of_business" name="line_of_business" placeholder="Enter line of business" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label required mb-2" for="vat_status">VAT Status</label>
                                <select id="vat_status" name="vat_status" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="Select VAT status">
                                    <option value=""></option>
                                    <option value="VAT-Registered">VAT-Registered</option>
                                    <option value="Non-VAT">Non-VAT</option>
                                    <option value="VAT-Exempt">VAT-Exempt</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="fiscal_year_start_month">Fiscal Year Start Month</label>
                                <select id="fiscal_year_start_month" name="fiscal_year_start_month" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-hide-search="true" data-placeholder="Select fiscal year start month">
                                    <option value=""></option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-2"></div>

                    <div class="d-flex flex-column gap-5">
                        <h6 class="fw-bold text-gray-800 m-0 d-flex align-items-center">
                            <i class="ki-duotone ki-geolocation fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Company Address
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

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="currency_id">Currency</label>
                                <select id="currency_id" name="currency_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="true" data-placeholder="Select currency"></select>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-2"></div>

                    <div class="d-flex flex-column gap-5">
                        <h6 class="fw-bold text-gray-800 m-0 d-flex align-items-center">
                            <i class="ki-duotone ki-address-book fs-4 me-2 text-primary">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                            Company Contact Information
                        </h6>

                        <div class="row g-5">
                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="phone">Phone</label>
                                <input type="text" class="form-control form-control-sm" id="phone" name="phone" placeholder="Enter phone" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="email">Email</label>
                                <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="Enter email" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="website">Website</label>
                                <input type="text" class="form-control form-control-sm" id="website" name="website" placeholder="Enter website" maxlength="100" autocomplete="off">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-2" for="contact_person">Contact Person</label>
                                <input type="text" class="form-control form-control-sm" id="contact_person" name="contact_person" placeholder="Enter contact person" maxlength="100" autocomplete="off">
                            </div>
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

