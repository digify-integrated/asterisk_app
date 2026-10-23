@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body py-5 px-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>

                <div class="d-flex align-items-center justify-content-start flex-wrap gap-2">
                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent  
                    
                    @component('partials.filter-button')
                        @slot('drawerId', 'address-type-filter-drawer')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'address-type-table-column-dropdown')
                        @slot('dropdownButtonId', 'address-type-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-1 pb-0 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'address-type-table')
            @endcomponent
        </div>

        @component('partials.form-modal')
            @slot('formTitle', 'Address Type Details')
            @slot('formId', 'address_type_form')
            @slot('size', 'md')

            <input type="hidden" id="address_type_id" name="address_type_id" />

            <div class="d-flex flex-column gap-7">
                <div class="row g-5">
                    <div class="col-12">
                        <label class="form-label required mb-2" for="name">Name</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                    </div>
                </div>
            </div>
        @endcomponent
    </div>

    @component('partials.filter-module')
        @slot('drawerId', 'address-type-filter-drawer')
        @slot('resetFilterId', 'address-type-reset-filters-btn')
        @slot('applyFilterId', 'address-type-apply-filters-btn')
    @endcomponent

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
@endpush

