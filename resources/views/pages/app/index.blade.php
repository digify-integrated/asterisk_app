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
                        @slot('collapseId', 'app-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'app-table-column-dropdown')
                        @slot('dropdownButtonId', 'app-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'app-filter-collapse')
                @slot('resetFilterId', 'app-reset-filters-btn')
                @slot('applyFilterId', 'app-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'app-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'App Details')
                @slot('formId', 'app_form')
                @slot('size', 'md')
                
                <input type="hidden" id="app_id" name="app_id" />

                <div class="d-flex flex-column gap-7">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-2" for="logo">Logo</label>
                            <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="name">Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Enter name" maxlength="100" autocomplete="off">
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label required mb-2" for="order_sequence">Order Sequence</label>
                            <input type="number" class="form-control form-control-sm" id="order_sequence" name="order_sequence" placeholder="0" min="0" max="100">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label required mb-2" for="description">Description</label>
                            <textarea class="form-control form-control-sm" id="description" name="description" rows="3" placeholder="Briefly describe the app..." maxlength="500"></textarea>
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

