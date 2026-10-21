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
                        @slot('collapseId', 'id-type-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'id-type-table-column-dropdown')
                        @slot('dropdownButtonId', 'id-type-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'id-type-filter-collapse')
                @slot('resetFilterId', 'id-type-reset-filters-btn')
                @slot('applyFilterId', 'id-type-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-1 pb-0 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'id-type-table')
            @endcomponent
        </div>

        @component('partials.form-modal')
            @slot('formTitle', 'ID Type Details')
            @slot('formId', 'id_type_form')
            @slot('size', 'md')

            <input type="hidden" id="id_type_id" name="id_type_id" />

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

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
@endpush

