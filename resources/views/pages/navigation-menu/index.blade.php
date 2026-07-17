@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-7">
        <div class="card-body py-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-5">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>

                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">

                    @if($deletePermission)
                        <div class="d-flex align-items-center grow-0 action-dropdown d-none">        
                            <a href="#" class="btn btn-light-dark btn-sm btn-flex btn-center btn-active-light-primary show menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Actions
                                <i class="ki-outline ki-down fs-7 ms-1"></i>
                            </a>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="javascript:void(0);" class="menu-link px-3 text-hover-danger" id="delete-data">
                                        <i class="ki-outline ki-trash fs-6 me-2 text-danger"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle cursor-pointer table-row-dashed gy-3" id="app-table">
                    <thead>
                        <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                            <th>
                                <div class="form-check form-check-sm ms-5">
                                    <input class="form-check-input datatable-checkbox-master" type="checkbox">
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Icon</th>
                            <th>Parent</th>
                            <th>Page Type</th>
                            <th>Order Sequence</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-800"></tbody>
                </table>
            </div>
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Navigation Menu Details')
                @slot('formId', 'navigation_menu_form')
                @slot('size', 'md')
                
                <input type="hidden" id="app_id" name="app_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="page_type">Page Type</label>
                            <select id="page_type" name="page_type" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true">
                                <option value="menu">Menu</option>
                                <option value="single_page">Single Page</option>
                                <option value="multi_page">Multi Page</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="icon">Icon</label>
                            <select id="icon" name="icon" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false">
                                <option value="">--</option>
                                @include('partials.icon-options')
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-2" for="parent_id">Icon</label>
                            <select id="parent_id" name="parent_id" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false">
                                <option value="">--</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="order_sequence">Order Sequence</label>
                            <input type="number" class="form-control form-control-sm" id="order_sequence" name="order_sequence" placeholder="0" min="0" max="100">
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

