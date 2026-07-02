<div id="log-notes-modal" class="modal fade" tabindex="-1" aria-labelledby="log-notes-modal-title" aria-hidden="true">    
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-sm">
            <div class="modal-header py-4 px-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-35px symbol-circle bg-light-primary d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
                        <i class="ki-outline ki-time fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="modal-title fw-bold text-gray-900 fs-5" id="log-notes-modal-title">System Audit Trails</h3>
                        <p class="text-muted fs-8 mb-0">Track real-time data adjustments and state history records</p>
                    </div>
                </div>
                
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-outline ki-cross fs-2"></i>
                </button>
            </div>

            <div class="modal-body py-6 px-9">                
                <div class="timeline timeline-border-dashed" id="log-notes"></div>

                <div id="log-notes-empty" class="d-none text-center py-12">
                    <div class="bg-light-secondary rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-4">
                        <i class="ki-outline ki-file-sheet fs-3x text-gray-400"></i>
                    </div>
                    <h5 class="fw-bold text-gray-800 fs-6">No log entries found</h5>
                    <p class="text-muted fs-7 max-w-300px mx-auto mb-0">There are currently no modifications recorded for this entry line item.</p>
                </div>
            </div>
        </div>
    </div>
</div>