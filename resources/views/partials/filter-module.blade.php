<div class="collapse" id="{{ $collapseId }}">
    <div class="pt-4 mt-4 border-top border-gray-200">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fs-7 text-uppercase fw-bold text-gray-600 tracking-wide">
                <i class="ki-outline ki-filter-search me-1"></i> Filter Options
            </span>
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
                Apply Filters
            </button>
        </div>
        
    </div>
</div>