<div id="system-error-modal" class="modal fade" tabindex="-1" aria-labelledby="system-error-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-50px">
                        <div class="symbol-label bg-danger-subtle">
                            <i class="ki-duotone ki-information-4 fs-1 text-danger"></i>
                        </div>
                    </div>

                    <div>
                        <h3 id="system-error-modal-label" class="mb-1">
                            Something went wrong
                        </h3>
                        <div class="text-muted">
                            An unexpected system error occurred.
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-4"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-light-danger d-flex align-items-start">
                    <i class="ki-duotone ki-shield-cross fs-2 text-danger me-3"></i>

                    <div>
                        <div class="fw-bold mb-1">
                            Error details
                        </div>

                        <div class="text-muted">
                            Please copy the information below and send it to your support team.
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-semibold">Technical Information</label>

                    <div class="bg-light border rounded p-4">
                        <pre id="error-dialog" class="mb-0 text-danger small" style="max-height: 350px; overflow:auto; white-space: pre-wrap;"></pre>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" class="btn btn-sm btn-light me-2" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="button" class="btn btn-sm btn-primary" id="copy-error-message">
                        <i class="ki-duotone ki-copy fs-5 me-1"></i>
                        Copy Error Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>