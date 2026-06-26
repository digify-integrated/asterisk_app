<div id="form-modal" class="modal fade" tabindex="-1" aria-labelledby="modal-title-{{ $formId }}" aria-hidden="true" data-bs-keyboard="false">    
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content shadow-sm">            
            <div class="modal-header py-5">
                <h3 class="modal-title fw-bold text-gray-900" id="modal-title-{{ $formId }}">{{ $formTitle }}</h3>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-3"></i>
                </button>
            </div>

            <div class="modal-body py-7">
                <form id="{{ $formId }}" method="post" action="#" autocomplete="off">
                    @csrf
                    {{ $slot }}
                </form>
            </div>

            <div class="modal-footer d-flex justify-content-end gap-2 py-5">
                <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal">Cancel</button>                
                <button type="submit" form="{{ $formId }}" class="btn btn-primary btn-sm fw-semibold" id="submit-data">Submit</button>
            </div>            
        </div>
    </div>
</div>