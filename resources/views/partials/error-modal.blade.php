<div id="system-error-modal" class="modal fade" tabindex="-1" aria-labelledby="system-error-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 px-8 pt-8 pb-5">
                <div class="d-flex align-items-start gap-4">
                    <div class="symbol symbol-50px shrink-0">
                        <div class="symbol-label bg-light-danger">
                            <i class="ki-outline ki-information-4 fs-2 text-danger"></i>
                        </div>
                    </div>

                    <div>
                        <h2 id="system-error-modal-label" class="fw-bold fs-3 text-gray-900 mb-2">Unexpected System Error</h2>

                        <p class="text-muted fs-7 mb-0">
                            Something prevented this request from completing.
                            You can copy the diagnostic information below and
                            send it to your system administrator.
                        </p>
                    </div>
                </div>

                <button type="button" class="btn btn-icon bg-transparent btn-sm rounded-circle" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-4"></i>
                </button>
            </div>

            <div class="border-top"></div>

            <div class="modal-body px-8 py-7">
                <div class="mb-6">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-semibold text-gray-900">
                                Technical Details
                            </div>

                            <div class="text-muted fs-7">
                                Visible only to assist with troubleshooting.
                            </div>
                        </div>

                        <span class="badge badge-light-danger">Internal Error</span>
                    </div>

                    <div class="border rounded bg-light p-5">
                        <pre id="error-dialog" class="mb-0 fs-8 text-gray-800" style="max-height:400px;overflow:auto;white-space:pre-wrap;font-family:var(--bs-font-monospace);"></pre>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 bg-light rounded p-4">
                    <i class="ki-outline ki-information-4 fs-4 text-muted mt-1"></i>
                    <div>
                        <div class="fw-semibold mb-1">
                            Need help?
                        </div>

                        <div class="text-muted fs-7">
                            Include the error details when contacting your
                            administrator so the issue can be investigated
                            more quickly.
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-8 pb-8 pt-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    Close
                </button>

                <button type="button" class="btn btn-primary btn-sm" id="copy-error-message">
                    <i class="ki-outline ki-copy fs-5 me-2"></i>

                    Copy Technical Details
                </button>
            </div>
        </div>
    </div>
</div>