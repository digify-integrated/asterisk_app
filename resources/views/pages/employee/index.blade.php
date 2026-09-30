@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="d-flex flex-column gap-7 max-w-1000px mx-auto pb-10">
        @component('partials.form-card')
            @slot('formId', 'account_profile_form')
            @slot('formTitle', 'General Details')
            @slot('formDescription', 'Update your public profile information and avatar')
            @slot('footerDescription', 'Please use a maximum of 32 characters for your display name.')
            @slot('submitButtonId', 'submit-profile')

            <div class="d-flex align-items-center gap-5 mb-7">
                <div class="symbol symbol-75px symbol-circle">
                    <img src="{{ auth()->user()->profile_picture_url }}" 
                        alt="Profile Picture" 
                        id="profile_picture_image" />
                </div>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2">
                        <label class="btn btn-sm btn-light-primary fw-semibold" for="profile_picture">
                            Upload Photo
                        </label>
                        <button type="button" class="btn btn-sm btn-light-danger fw-semibold" data-image-reset data-target="#profile_picture">
                            Remove
                        </button>
                        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*" 
                            data-image-preview 
                            data-target="#profile_picture_image" />
                    </div>
                    <span class="text-gray-500 fs-8">Allowed formats: PNG, JPG, JPEG. Max 2MB.</span>
                </div>
            </div>

            <div class="separator separator-dashed my-6"></div>

            <div class="row g-5">
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold text-gray-700 fs-7" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ auth()->user()->name ?? '' }}" placeholder="Enter name" />
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold text-gray-700 fs-7" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control form-control-sm" value="{{ auth()->user()->email ?? '' }}" placeholder="Enter email" />
                </div>
            </div>
        @endcomponent

        @component('partials.form-card')
            @slot('formId', 'account_security_form')
            @slot('formTitle', 'Security')
            @slot('formDescription', 'Update your password to keep your account safe')
            @slot('footerDescription', 'Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character.')
            @slot('submitButtonId', 'submit-password')

            <div class="row g-5">
                <div class="col-12">
                    <label class="form-label required fw-semibold text-gray-700 fs-7" for="current_password">Current Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" id="current_password" name="current_password" class="form-control bg-transparent" placeholder="Enter your current password">

                        <span class="input-group-text bg-transparent cursor-pointer password-addon" data-password-toggle data-target="#current_password">
                            <i class="fs-4 ki-outline ki-eye pe-none"></i>
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold text-gray-700 fs-7" for="new_password">New Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" id="new_password" name="new_password" class="form-control bg-transparent" placeholder="Enter your new password">

                        <span class="input-group-text bg-transparent cursor-pointer password-addon" data-password-toggle data-target="#new_password">
                            <i class="fs-4 ki-outline ki-eye pe-none"></i>
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold text-gray-700 fs-7" for="new_password_confirmation">Confirm New Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control bg-transparent" placeholder="Comfirm your new password">

                        <span class="input-group-text bg-transparent cursor-pointer password-addon" data-password-toggle data-target="#new_password_confirmation">
                            <i class="fs-4 ki-outline ki-eye pe-none"></i>
                        </span>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
@endsection

@push('scripts')
    @if (!empty($jsFile))
        <script type="module" src="{{ asset('assets/js/pages/' . $jsFile . '.js') }}"></script>
    @endif
@endpush

