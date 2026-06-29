@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-7">
        <div class="card-body py-5">

            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-5">

                {{-- LEFT --}}
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 flex-grow-1">

                    @include('partials.datatable-search')

                </div>

                {{-- RIGHT --}}
                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">

                    @if($deletePermission)
                        <div class="d-flex align-items-center grow-0 action-dropdown">                        
                            <a href="#" class="btn btn-dark btn-sm btn-flex btn-center btn-active-light-primary show menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
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
        <div class="card-body pt-5 pb-5 pe-0 ps-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle cursor-pointer table-row-dashed gy-3" id="app-table">
                    <thead>
                        <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                            <th>
                                <div class="form-check form-check-sm ms-5">
                                    <input class="form-check-input" id="datatable-checkbox" type="checkbox">
                                </div>
                            </th>
                            <th>App</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-800"></tbody>
                </table>
            </div>
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'App Details')
                @slot('formId', 'app_form')

                
                <input type="hidden" id="app_id" name="app_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-3" for="app_logo">App Logo</label>
                            <input type="file" class="form-control form-control-sm" id="app_logo" name="app_logo" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-12 col-md-4">
                            <label class="form-label required mb-3" for="app_name">Display Name</label>
                            <input type="text" class="form-control form-control-sm" id="app_name" name="app_name" placeholder="Enter app display name" maxlength="100" autocomplete="off">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label required mb-3" for="navigation_menu_id">Landing Page</label>
                            <select id="navigation_menu_id" name="navigation_menu_id" data-dropdown-parent="#form-modal" class="form-select form-select-sm" data-placeholder="Select a landing page" data-control="select2" data-allow-clear="true">
                                <option></option>
                            </select>
                        </div>
                        
                        <div class="col-12 col-md-4">
                            <label class="form-label required mb-3" for="order_sequence">Order Sequence</label>
                            <input type="number" class="form-control form-control-sm" id="order_sequence" name="order_sequence" placeholder="0" min="0" max="100">
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-12">
                            <label class="form-label required mb-3" for="app_description">Description</label>
                            <textarea class="form-control form-control-sm" id="app_description" name="app_description" rows="3" placeholder="Briefly describe the application..." maxlength="500"></textarea>
                        </div>
                    </div>
                </div>
            @endcomponent
        @endif
    </div>

    @if(($exportPermission ?? 0) > 0)
        @include('partials.export-modal')
    @endif

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js') }}"></script>

    @if (!empty($jsFile))
        <script type="module" src="{{ asset('assets/js/pages/' . $jsFile . '.js') }}"></script>
    @endif
@endpush

