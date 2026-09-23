<div class="collapse" id="{{ $collapseId }}">
    <div class="pt-4 mt-4 border-top border-gray-200">
        
        <!-- Filter Options Header & Saved Filters Trigger Menu -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fs-7 text-uppercase fw-bold text-gray-600 tracking-wide">
                <i class="ki-outline ki-filter-search me-1"></i> Filter Options
            </span>

            <!-- Metronic Saved Filters Dropdown Trigger -->
            <div class="dropdown">
                <button class="btn btn-sm btn-warning" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true" title="Saved Filters">
                    Saved Filter
                </button>

                <!-- Menu Content -->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-250px p-3" data-kt-menu="true">
                    <div class="menu-item mb-2">
                        <div class="menu-content text-muted pb-2 fs-7 fw-bold text-uppercase border-bottom">Saved Filters</div>
                    </div>

                    <!-- List of Saved Filters (Populated dynamically via JS) -->
                    <div id="saved_filters_list" class="menu-item mb-2">
                        <span class="text-muted fs-8 px-2">No saved filters yet</span>
                    </div>

                    <div class="separator my-2"></div>

                    <!-- Save Current Filter Form Section -->
                    <div class="menu-item px-2">
                        <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Save Current Filter</label>
                        <input type="text" id="save_filter_name" class="form-control form-control-sm mb-2" placeholder="Preset name..." />
                        
                        <div class="form-check form-check-custom form-check-solid form-check-sm mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="set_as_default_checkbox" />
                            <label class="form-check-label fs-8 fw-semibold text-gray-700" for="set_as_default_checkbox">
                                Set as default
                            </label>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary w-100 fw-bold" id="save_filter_btn">
                            Save Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Fields Slot -->
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

        <!-- Action Buttons -->
        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top border-gray-200">
            <button type="button" class="btn btn-sm btn-light btn-active-light-danger fw-semibold" id="{{ $resetFilterId }}">
                Reset
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-bold" id="{{ $applyFilterId }}">
                Apply Filters
            </button>
        </div>
        
    </div>
</div>