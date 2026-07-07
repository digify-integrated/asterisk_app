<div id="log-notes-modal"
     class="modal fade"
     tabindex="-1"
     aria-labelledby="log-notes-modal-title"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 px-9 pt-8 pb-6">

                <div class="d-flex align-items-start gap-4">

                    <div class="app-icon-sm">
                        <i class="ki-outline ki-time fs-3 text-primary"></i>
                    </div>

                    <div>
                        <h2 class="fw-bold fs-3 text-dark mb-1"
                            id="log-notes-modal-title">
                            Audit Trail
                        </h2>

                        <div class="text-muted fs-7">
                            View every change made to this record, including status updates,
                            field modifications, and user activity.
                        </div>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-icon btn-sm btn-light btn-active-light-primary rounded-circle"
                    data-bs-dismiss="modal">

                    <i class="ki-outline ki-cross fs-2"></i>

                </button>

            </div>

            <!-- Divider -->
            <div class="border-top"></div>

            <!-- Body -->
            <div class="modal-body px-9 py-8">

                <div
                    class="timeline timeline-border-dashed"
                    id="log-notes">
                </div>

                <!-- Empty -->
                <div
                    id="log-notes-empty"
                    class="d-none">

                    <div class="py-16 text-center">

                        <div class="empty-icon mb-6">

                            <i class="ki-outline ki-file-sheet fs-2hx text-muted"></i>

                        </div>

                        <h4 class="fw-bold text-dark mb-2">
                            No audit history
                        </h4>

                        <p class="text-muted fs-7 mx-auto"
                           style="max-width:420px">

                            Once users begin editing this record,
                            every important activity will appear here
                            in chronological order.

                        </p>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>