<div id="{{ $collapseId }}" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#filter_drawer_toggle" data-kt-drawer-close="#filter_drawer_close" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px'}">
    <div class="card rounded-0 w-100 h-100">
        <div class="card-header pe-5">
            <div class="card-title">
                <i class="ki-duotone ki-filter fs-2 me-2"></i>
                <span class="fs-4 fw-bold text-gray-800">
                    Filter Options
                </span>
            </div>

            <div class="card-toolbar">
                <div class="btn btn-sm btn-icon" id="filter_drawer_close">
                    <i class="ki-duotone ki-cross fs-2"></i>
                </div>
            </div>
        </div>

        <div class="card-body hover-scroll-overlay-y px-6 py-5">
            <div class="accordion mb-6" id="presets_accordion">
                <div class="accordion-item shadow-xs rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="presets_accordion_header">
                        <button class="accordion-button fs-7 fw-bold text-gray-800 py-4 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#presets_accordion_body" aria-expanded="false" aria-controls="presets_accordion_body">
                            <span class="d-flex align-items-center">
                                <i class="ki-duotone ki-save-2 fs-3 me-2"></i>
                                <span>Saved Presets</span>
                            </span>
                            <span class="badge badge-light-dark badge-sm fw-bold ms-2" id="saved_filters_count">0</span>
                        </button>
                    </h2>

                    <div id="presets_accordion_body" class="accordion-collapse collapse" aria-labelledby="presets_accordion_header" data-bs-parent="#presets_accordion">
                        <div class="accordion-body p-4">
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fs-8 fw-bold text-uppercase text-gray-500 tracking-wider">Available Presets</span>
                                    <span class="fs-8 text-muted">Click to apply</span>
                                </div>

                                <div id="saved_filters_container" class="rounded-2 border border-gray-200 p-2" style="max-height: 180px; overflow-y: auto; overflow-x: hidden;">
                                    <div id="saved_filters_list">
                                        <div class="text-center py-4 px-2">
                                            <i class="ki-duotone ki-filter-search fs-2 text-gray-400 mb-1"></i>
                                            <span class="text-muted fs-8 d-block fw-semibold">No saved filters yet</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="rounded-2 p-3.5">
                                <label for="save_filter_name" class="form-label fs-8 fw-bold text-gray-700 mb-2">Save Current Filter State</label>

                                <div class="input-group input-group-sm mb-2.5">
                                    <span class="input-group-text">
                                        <i class="ki-duotone ki-bookmark fs-6 text-gray-500"></i>
                                    </span>
                                    <input type="text" id="save_filter_name" class="form-control form-control-sm" placeholder="Enter filter name" autocomplete="off" />
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input" type="checkbox" value="1" id="set_as_default_checkbox" />
                                        <label class="form-check-label fs-8 fw-semibold text-gray-700 ms-2" for="set_as_default_checkbox">
                                            Set as default filter
                                        </label>
                                    </div>

                                   <button type="button" class="btn btn-sm btn-light-dark fw-bold" id="save_filter_btn">
                                        Save Preset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="separator separator-dashed mb-5"></div>

            <div class="mb-4">
                <h3 class="fs-7 fw-bold text-uppercase text-gray-500 tracking-wider mb-0">Filter Parameters</h3>
            </div>
            <div class="row g-4">
                {{ $slot }}

                <div class="col-12">
                    <label class="form-label fs-7 fw-semibold text-gray-700 mb-1" for="filter_created_date">Created Date</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="ki-duotone ki-calendar fs-5 position-absolute ms-3 text-gray-500"></i>
                        <input type="text" id="filter_created_date" name="filter_created_date" class="form-control form-control-sm ps-10" placeholder="Pick date range" autocomplete="off"/>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-light px-6 py-4 d-flex align-items-center justify-content-start gap-2">
            <button type="button" class="btn btn-sm btn-light-dark" id="{{ $applyFilterId }}">
                Apply Filter
            </button>
            <button type="button" class="btn btn-sm btn-light-danger" id="{{ $resetFilterId }}">
                Reset
            </button>
        </div>
    </div>
</div>