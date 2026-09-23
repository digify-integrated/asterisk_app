<form id="{{ $formId }}" method="POST" action="#" autocomplete="off">
    @csrf
    
    <div class="card settings-card shadow-none">
        <div class="card-body p-6 p-lg-8">
            <div class="mb-6">
                <h3 class="fw-bold text-gray-900 fs-4 mb-1">{{ $formTitle }}</h3>
                
                @isset($formDescription)
                    <p class="text-gray-500 fs-7 mb-0">{{ $formDescription }}</p>
                @endisset
            </div>

            {{ $slot }}
        </div>

        <div class="card-footer settings-card-footer d-flex align-items-center px-6 py-4">
            @isset($footerDescription)
                <span class="text-gray-500 fs-8 text-wrap pe-4 mw-50">
                    {{ $footerDescription }}
                </span>
            @endisset

            <div class="d-flex align-items-center ms-auto">
                @isset($formButtonSlot)
                    <div class="me-2">
                        {{ $formButtonSlot }}
                    </div>
                @endisset
                
                <button type="submit" class="btn btn-primary btn-sm" form="{{ $formId }}" id="{{ $submitButtonId }}">Save Changes</button>
            </div>
        </div>
    </div>
</form>