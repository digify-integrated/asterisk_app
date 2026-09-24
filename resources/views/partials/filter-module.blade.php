<div class="collapse" id="{{ $collapseId }}">
    <div class="pt-4 mt-4 border-top border-gray-200">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fs-7 text-uppercase fw-bold text-gray-600 tracking-wide">
                <i class="ki-outline ki-filter-search me-1"></i> Filter Options
            </span>

            <div class="dropdown">
                <button class="btn btn-sm btn-light-dark fw-bold px-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true" title="Saved Filters">
                    <i class="ki-outline ki-save-2 fs-6 me-1"></i> Saved Filters
                </button>

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-300px p-4 shadow-sm" data-kt-menu="true">                    
                    <div class="menu-item mb-2">
                        <div class="menu-content text-muted pb-2 fs-8 fw-bold text-uppercase border-bottom d-flex justify-content-between align-items-center">
                            <span>Presets</span>
                            <span class="badge badge-light fs-8" id="saved_filters_count">0</span>
                        </div>
                    </div>
                    
                    <div id="saved_filters_container" class="mb-2" style="max-height: 210px; overflow-y: auto; overflow-x: hidden;">
                        <div id="saved_filters_list">
                            <span class="text-muted fs-8 px-2 py-3 d-block text-center">No saved filters yet</span>
                        </div>
                    </div>

                    <div class="separator my-3"></div>

                    <div class="menu-item px-1">
                        <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Save Current Filter</label>
                        <input type="text" id="save_filter_name" class="form-control form-control-sm mb-2" placeholder="Enter filter name" autocomplete="off" />
                        
                        <div class="form-check form-check-custom form-check-solid form-check-sm mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="set_as_default_checkbox" />
                            <label class="form-check-label fs-8 fw-semibold text-gray-700" for="set_as_default_checkbox">
                                Set as default filter
                            </label>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary w-100 fw-bold" id="save_filter_btn">
                            <i class="ki-outline ki-plus fs-6 me-1"></i> Save Preset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{ $slot }}

            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_created_date">Created Date</label>
                <div class="position-relative d-flex align-items-center">
                    <i class="ki-outline ki-calendar fs-6 position-absolute ms-3 text-gray-500"></i>
                    <input type="text" id="filter_created_date" name="filter_created_date" class="form-control form-control-sm ps-10" placeholder="Pick date range" autocomplete="off"/>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top border-gray-200">
            <button type="button" class="btn btn-sm btn-light btn-active-light-danger fw-semibold" id="{{ $resetFilterId }}">
                Reset
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-bold" id="{{ $applyFilterId }}">
                Apply Filter
            </button>
        </div>
        
    </div>
</div>