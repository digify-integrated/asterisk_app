<div id="export-modal" class="modal fade" tabindex="-1" aria-labelledby="export-modal-title" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content shadow-sm">
            
            <div class="modal-header py-5">
                <h3 class="modal-title fw-bold text-gray-900" id="export-modal-title">Export Data</h3>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-3"></i>
                </button>
            </div>

            <div class="modal-body py-7 px-lg-10">
                
                <div class="mb-8">
                    <label class="fs-6 fw-bold text-gray-800 mb-3 d-block" for="export_to">
                        1. Select Export Format
                    </label>
                    
                    <div class="row g-4" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                        <div class="col-6 col-md-3">
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary active d-flex align-items-center p-5 cursor-pointer h-100" data-kt-button="true">
                                <span class="form-check form-check-custom form-check-solid form-check-sm me-4">
                                    <input class="form-check-input" type="radio" name="export_to" value="csv" checked="checked" />
                                </span>
                                <span>
                                    <span class="fs-5 fw-bold text-gray-800 d-block">CSV</span>
                                </span>
                            </label>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex align-items-center p-5 cursor-pointer h-100" data-kt-button="true">
                                <span class="form-check form-check-custom form-check-solid form-check-sm me-4">
                                    <input class="form-check-input" type="radio" name="export_to" value="csv import" />
                                </span>
                                <span>
                                    <span class="fs-5 fw-bold text-gray-800 d-block">CSV Import</span>
                                </span>
                            </label>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex align-items-center p-5 cursor-pointer h-100" data-kt-button="true">
                                <span class="form-check form-check-custom form-check-solid form-check-sm me-4">
                                    <input class="form-check-input" type="radio" name="export_to" value="xlsx" />
                                </span>
                                <span>
                                    <span class="fs-5 fw-bold text-gray-800 d-block">Excel</span>
                                </span>
                            </label>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex align-items-center p-5 cursor-pointer h-100" data-kt-button="true">
                                <span class="form-check form-check-custom form-check-solid form-check-sm me-4">
                                    <input class="form-check-input" type="radio" name="export_to" value="pdf" />
                                </span>
                                <span>
                                    <span class="fs-5 fw-bold text-gray-800 d-block">PDF</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="text-gray-200 my-6">

                <div>
                    <div class="mb-4">
                        <label class="fs-6 fw-bold text-gray-800 d-block mb-1">2. Configure Fields to Include</label>
                        <div class="text-muted fs-7">Move the columns you want to export from the available list to the selected list.</div>
                    </div>

                    <div class="row">
                        <div class="col-12 dual-listbox-container">
                            <select multiple="multiple" size="20" id="table_column" name="table_column[]"></select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer d-flex justify-content-end gap-3 py-5">
                <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="submit-export">Generate Export</button>
            </div>
            
        </div>
    </div>
</div>