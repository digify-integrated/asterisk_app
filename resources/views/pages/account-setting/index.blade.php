@extends('layouts.module')

@push('css')
    <style>
        /* Minimalist Vercel/Supabase-inspired tweaks using standard Bootstrap/Metronic variables */
        .settings-card {
            border: 1px solid var(--bs-gray-200);
            border-radius: 0.75rem;
            transition: border-color 0.15s ease;
        }
        .settings-card-footer {
            background-color: var(--bs-gray-100);
            border-top: 1px solid var(--bs-gray-200);
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
        }
        .danger-card {
            border: 1px solid var(--bs-danger-light, #f8d7da);
        }
        .danger-card .settings-card-footer {
            background-color: var(--bs-danger-light, #fff5f8);
            border-top: 1px solid var(--bs-danger-light, #f8d7da);
        }
    </style>
@endpush

@section('content')
<div class="d-flex flex-column gap-7 max-w-1000px mx-auto pb-10">

    {{-- Header & Sub-navigation --}}
    <div class="d-flex flex-column gap-2">
        <h1 class="fw-bold text-gray-900 fs-2 mb-0">Account Settings</h1>
        <p class="text-gray-600 fs-6 mb-0">Manage your profile, security credentials, and preferences.</p>
    </div>

    {{-- Profile Section --}}
    <form id="account_profile_form" action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card settings-card shadow-none">
            <div class="card-body p-6 p-lg-8">
                <div class="mb-6">
                    <h3 class="fw-bold text-gray-900 fs-4 mb-1">General Details</h3>
                    <p class="text-gray-500 fs-7 mb-0">Update your public profile information and avatar.</p>
                </div>

                {{-- Avatar --}}
                <div class="d-flex align-items-center gap-5 mb-7">
                    <div class="symbol symbol-75px symbol-circle">
                        <img src="{{ auth()->user()->avatar_url ?? asset('assets/media/avatars/blank.png') }}" alt="Avatar" id="avatar_preview" />
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex gap-2">
                            <label class="btn btn-sm btn-light-primary fw-semibold" for="avatar_input">
                                Upload Photo
                            </label>
                            <input type="file" id="avatar_input" name="avatar" class="d-none" accept="image/*" />
                            <button type="button" class="btn btn-sm btn-light-danger fw-semibold" id="remove_avatar_btn">Remove</button>
                        </div>
                        <span class="text-gray-500 fs-8">Allowed formats: PNG, JPG, JPEG. Max 2MB.</span>
                    </div>
                </div>

                <div class="separator separator-dashed my-6"></div>

                {{-- Name & Email Inputs --}}
                <div class="row g-5">
                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-semibold text-gray-700 fs-7" for="name">Display Name</label>
                        <input type="text" id="name" name="name" class="form-control form-control-solid form-control-sm" value="{{ auth()->user()->name ?? '' }}" required />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-semibold text-gray-700 fs-7" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control form-control-solid form-control-sm" value="{{ auth()->user()->email ?? '' }}" required />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-gray-700 fs-7" for="timezone">Timezone</label>
                        <select id="timezone" name="timezone" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="Select timezone">
                            <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                            <option value="Asia/Manila">Asia/Manila (PHT)</option>
                            <option value="America/New_York">America/New_York (EST)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-footer settings-card-footer d-flex align-items-center justify-content-between px-6 py-4">
                <span class="text-gray-500 fs-8">Please use a maximum of 32 characters for your display name.</span>
                <button type="submit" class="btn btn-sm btn-dark fw-semibold" id="save_profile_btn">Save Changes</button>
            </div>
        </div>
    </form>

    {{-- Security / Password Section --}}
    <form id="account_security_form" action="#" method="POST">
        @csrf
        <div class="card settings-card shadow-none">
            <div class="card-body p-6 p-lg-8">
                <div class="mb-6">
                    <h3 class="fw-bold text-gray-900 fs-4 mb-1">Security</h3>
                    <p class="text-gray-500 fs-7 mb-0">Update your password to keep your account safe.</p>
                </div>

                <div class="row g-5">
                    <div class="col-12">
                        <label class="form-label required fw-semibold text-gray-700 fs-7" for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control form-control-solid form-control-sm" autocomplete="current-password" />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-semibold text-gray-700 fs-7" for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control form-control-solid form-control-sm" autocomplete="new-password" />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-semibold text-gray-700 fs-7" for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-solid form-control-sm" autocomplete="new-password" />
                    </div>
                </div>
            </div>

            <div class="card-footer settings-card-footer d-flex align-items-center justify-content-between px-6 py-4">
                <span class="text-gray-500 fs-8">Passwords must be at least 8 characters long.</span>
                <button type="submit" class="btn btn-sm btn-dark fw-semibold" id="update_password_btn">Update Password</button>
            </div>
        </div>
    </form>

    {{-- Danger Zone Section --}}
    <div class="card settings-card danger-card shadow-none">
        <div class="card-body p-6 p-lg-8">
            <h3 class="fw-bold text-danger fs-4 mb-1">Delete Account</h3>
            <p class="text-gray-600 fs-7 mb-0">
                Permanently remove your account and all associated personal data. This action is irreversible.
            </p>
        </div>

        <div class="card-footer settings-card-footer d-flex align-items-center justify-content-between px-6 py-4">
            <span class="text-gray-500 fs-8">Transfers ownership of all shared resources before deletion.</span>
            <button type="button" class="btn btn-sm btn-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#delete_account_modal">
                Delete Account
            </button>
        </div>
    </div>

</div>

{{-- Confirmation Modal for Account Deletion --}}
<div class="modal fade" id="delete_account_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-500px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-gray-900 fw-bold">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-gray-600 fs-7 mb-4">
                    Are you sure you want to delete your account? Type <strong class="text-gray-900">DELETE</strong> below to confirm.
                </p>
                <input type="text" id="delete_confirmation_input" class="form-control form-control-solid form-control-sm" placeholder="TYPE DELETE" />
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-light fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger fw-semibold" id="confirm_delete_account_btn" disabled>Confirm Delete</button>
            </div>
        </div>
    </div>
</div>

{{-- Optional Log Notes Modal --}}
@include('partials.log-notes-modal')
@endsection

@push('scripts')
    @if (!empty($jsFile))
        <script type="module" src="{{ asset('assets/js/pages/' . $jsFile . '.js') }}"></script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete confirmation button state
            const deleteInput = document.getElementById('delete_confirmation_input');
            const deleteBtn = document.getElementById('confirm_delete_account_btn');

            if (deleteInput && deleteBtn) {
                deleteInput.addEventListener('input', function () {
                    deleteBtn.disabled = this.value.trim() !== 'DELETE';
                });
            }
        });
    </script>
@endpush