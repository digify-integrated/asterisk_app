<div id="form-modal"
     class="modal fade"
     tabindex="-1"
     aria-labelledby="modal-title-{{ $formId }}"
     aria-hidden="true"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

        <div class="modal-content border-0 rounded-4">

            {{-- Header --}}
            <div class="modal-header border-0 px-8 pt-8 pb-6">

                <div class="d-flex align-items-start gap-4">

                    <div class="symbol symbol-50px flex-shrink-0">
                        <div class="symbol-label bg-light-primary">

                            <i class="ki-outline ki-notepad-edit fs-2 text-primary"></i>

                        </div>
                    </div>

                    <div>

                        <h2
                            class="fw-bold fs-3 text-gray-900 mb-1"
                            id="modal-title-{{ $formId }}">

                            {{ $formTitle }}

                        </h2>

                        <p class="text-muted fs-7 mb-0">

                            Complete the required information below before saving.

                        </p>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-icon btn-light rounded-circle"
                    data-bs-dismiss="modal">

                    <i class="ki-outline ki-cross fs-3"></i>

                </button>

            </div>

            <div class="border-top"></div>

            {{-- Body --}}
            <div class="modal-body px-8 py-7">

                <form
                    id="{{ $formId }}"
                    method="POST"
                    action="#"
                    autocomplete="off">

                    @csrf

                    {{ $slot }}

                </form>

            </div>

            <div class="border-top"></div>

            {{-- Footer --}}
            <div class="modal-footer border-0 px-8 py-6">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    type="submit"
                    form="{{ $formId }}"
                    id="submit-data"
                    class="btn btn-primary">

                    Save Changes

                </button>

            </div>

        </div>

    </div>

</div>