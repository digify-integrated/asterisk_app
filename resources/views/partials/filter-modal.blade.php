<button type="button" class="btn btn-light-dark btn-sm btn-flex align-items-center table-export" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
    <i class="ki-outline ki-filter fs-5"></i>
    Filter
</button>

<div id="{{ $modalId }}" class="modal fade" tabindex="-1" aria-labelledby="modal-title-{{ $modalId }}" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-{{ $size }}">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 px-8 pt-8 pb-6">
                <div class="d-flex align-items-start gap-4">
                    <div class="symbol symbol-50px shrink-0">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-outline ki-filter fs-2 text-primary"></i>
                        </div>
                    </div>

                    <div>
                        <h2 class="fw-bold fs-3 text-gray-900 mb-1" id="modal-title-{{ $modalId }}">Filter Results</h2>
                        <p class="text-muted fs-7 mb-0">Refine your results using the filters below.</p>
                    </div>
                </div>

                <button type="button" class="btn btn-icon bg-transparent btn-sm rounded-circle" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-4"></i>
                </button>
            </div>

            <div class="border-top"></div>

            <div class="modal-body px-8 py-7">
                {{ $slot }}
            </div>

            <div class="border-top"></div>

            <div class="modal-footer border-0 px-8 py-6">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button" id="apply-filter" class="btn btn-success btn-sm">
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>