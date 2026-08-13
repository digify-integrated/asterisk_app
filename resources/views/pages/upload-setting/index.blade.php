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
                        @slot('collapseId', 'upload-setting-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'upload-setting-table-column-dropdown')
                        @slot('dropdownButtonId', 'upload-setting-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'upload-setting-filter-collapse')
                @slot('resetFilterId', 'upload-setting-reset-filters-btn')
                @slot('applyFilterId', 'upload-setting-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-3 pb-3 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'upload-setting-table')
            @endcomponent
        </div>

        @if($pageType == 'single_page')
            @component('partials.form-modal')
                @slot('formTitle', 'Upload Setting Details')
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
                            <label class="form-label required mb-2" for="max_file_size">Max File Size</label>
                            <input type="number" class="form-control form-control-sm" id="max_file_size" name="max_file_size" placeholder="0" min="1" step="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label mb-2" for="parent_id">Allowed Extensions</label>
                            <input class="form-control form-control-sm" id="extension" name="extension"/>
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

