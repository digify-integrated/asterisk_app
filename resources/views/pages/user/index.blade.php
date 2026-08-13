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
                        @slot('collapseId', 'user-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'user-table-column-dropdown')
                        @slot('dropdownButtonId', 'user-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'user-filter-collapse')
                @slot('resetFilterId', 'user-reset-filters-btn')
                @slot('applyFilterId', 'user-apply-filters-btn')

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
                @slot('tableId', 'user-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'User Account Details')
                @slot('formId', 'user_form')
                @slot('size', 'md')
                
                <input type="hidden" id="user_id" name="user_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-2" for="profile_picture">Profile Picture</label>
                            <input type="file" class="form-control form-control-sm" id="profile_picture" name="profile_picture" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="email">Email</label>
                            <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="Enter email" maxlength="200" autocomplete="off">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="password">Password</label>
                            <div class="input-group input-group-sm">
                                <input type="password" id="password" name="password" class="form-control bg-transparent" placeholder="Enter your password">

                                <span class="input-group-text bg-transparent cursor-pointer password-addon" 
                                    data-password-toggle 
                                    data-target="#password">
                                    <i class="fs-4 ki-outline ki-eye pe-none"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="status">Status</label>
                            <select id="status" name="status" class="form-select form-select-sm" data-dropdown-parent="#form-modal" data-control="select2" data-allow-clear="false" data-hide-search="true" data-placeholder="Select status">
                                <option value="Inactive">Inactive</option>
                                <option value="Active">Active</option>
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

