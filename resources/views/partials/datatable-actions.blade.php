<div class="d-flex align-items-center grow-0 action-dropdown d-none">        
    <a href="#" class="btn btn-light-dark btn-sm btn-flex btn-center btn-active-light-primary show menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
        Actions
        <i class="ki-outline ki-down fs-7 ms-1"></i>
    </a>

    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fs-7 w-125px py-4" data-kt-menu="true">
        {{ $slot }}
        
        @if($deletePermission)
            <div class="menu-item px-3">
                <a href="javascript:void(0);" class="menu-link px-3 text-hover-danger" id="delete-data">
                    <i class="ki-outline ki-trash fs-6 me-2 text-danger"></i>Delete
                </a>
            </div>
        @endif
    </div>
</div>