@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <form id="kt_hr_add_employee_form" class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" data-kt-redirect="/metronic8/demo8/?page=apps/hr/employees" method="POST" enctype="multipart/form-data">
    <!--begin::Modern Employee Form Wrapper-->
    <div class="card card-flush shadow-sm rounded-4 border-0 col-12 p-0">
    
        <!--begin::Wizard Header & Progress Steps-->
        <div class="card-header flex-column flex-md-row align-items-md-center pt-7 pb-5 px-5 px-lg-9 border-bottom gap-4">
            <div class="d-flex flex-column">
                <h1 class="text-gray-900 fw-bold fs-2 mb-1">Add New Employee Record</h1>
                <span class="text-muted fs-6">Fill out personal and employment details for onboarding.</span>
            </div>
            
            <!-- Stepper Navigation Pills -->
            <div class="card-toolbar w-100 w-md-auto overflow-auto pb-2 pb-md-0">
                <ul class="nav nav-pills nav-pills-custom gap-3 flex-nowrap" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link btn btn-active-light btn-color-muted fw-semibold py-2 px-3 px-lg-4 active d-flex align-items-center text-nowrap" data-bs-toggle="tab" href="#step_personal" aria-selected="true" role="tab">
                            <div class="text-start">
                                <span class="fs-8 text-muted d-block">Step 1</span>
                                <span class="fs-7 fs-lg-6 fw-bold text-gray-800">Personal Info</span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link btn btn-active-light btn-color-muted fw-semibold py-2 px-3 px-lg-4 d-flex align-items-center text-nowrap" data-bs-toggle="tab" href="#step_employment" aria-selected="false" role="tab" tabindex="-1">
                            <span class="bullet bullet-vertical h-30px bg-transparent me-2 me-lg-3"></span>
                            <div class="text-start">
                                <span class="fs-8 text-muted d-block">Step 2</span>
                                <span class="fs-7 fs-lg-6 fw-bold text-gray-800">Employment Details</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!--end::Wizard Header-->

        <!--begin::Form Body Content-->
        <div class="card-body p-5 p-lg-9">
            <div class="tab-content">
                
                <!-- ========================================== -->
                <!-- STEP 1: PERSONAL INFORMATION             -->
                <!-- ========================================== -->
                <div class="tab-pane fade active show" id="step_personal" role="tab-panel">
                    <!-- Row 1: Naming Details -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">First Name</label>
                            <input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g., John">
                        </div>
                        <div class="col-12 col-md-3 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control form-control-sm" placeholder="e.g., David">
                        </div>
                        <div class="col-12 col-md-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Last Name</label>
                            <input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g., Doe">
                        </div>
                        <div class="col-12 col-md-3 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Suffix</label>
                            <input type="text" name="suffix" class="form-control form-control-sm" placeholder="e.g., Jr.">
                        </div>
                    </div>

                    <!-- Row 2: Nickname -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Nickname / Preferred Name</label>
                            <input type="text" name="nickname" class="form-control form-control-sm" placeholder="e.g., Johnny">
                        </div>
                    </div>

                    <!-- Row 3: Contacts -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Email Address</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="ki-duotone ki-sms fs-4"><span class="path1"></span><span class="path2"></span></i></span>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="john.doe@example.com">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Phone Number</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="ki-duotone ki-phone fs-4"><span class="path1"></span><span class="path2"></span></i></span>
                                <input type="text" name="phone" class="form-control form-control-sm" placeholder="+1 (555) 000-0000">
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Birth Details -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Birthday</label>
                            <input type="date" name="birthday" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Place of Birth</label>
                            <input type="text" name="place_of_birth" class="form-control form-control-sm" placeholder="City, State, Country">
                        </div>
                    </div>

                    <!-- Row 5: Foreign IDs -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Marital Status</label>
                            <select name="marital_status_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select status">
                                <option></option>
                                <option value="1">Single</option>
                                <option value="2">Married</option>
                                <option value="3">Divorced</option>
                                <option value="4">Widowed</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Gender</label>
                            <select name="gender_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select gender">
                                <option></option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <option value="3">Non-binary</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Religion</label>
                            <select name="religion_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select religion">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 6: Physical Attributes -->
                    <div class="row g-5">
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Blood Type</label>
                            <select name="blood_type_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select blood type">
                                <option></option>
                                <option value="1">O+</option>
                                <option value="2">O-</option>
                                <option value="3">A+</option>
                                <option value="4">A-</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Height (cm)</label>
                            <input type="number" step="0.01" name="height" class="form-control form-control-sm" placeholder="175.50">
                        </div>
                        <div class="col-12 col-md-4 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" class="form-control form-control-sm" placeholder="70.00">
                        </div>
                    </div>
                </div>
                <!--end::Step 1-->

                <!-- ========================================== -->
                <!-- STEP 2: EMPLOYMENT DETAILS               -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="step_employment" role="tab-panel">
                    <div class="mb-7">
                        <h3 class="fs-4 fw-bold text-gray-800 mb-1">Employment & Organizational Structure</h3>
                        <p class="text-muted fs-7">Configure work credentials, designation, company placement, and timeline.</p>
                    </div>

                    <!-- Improved Modern Employee Photo / Avatar Upload Section -->
                    <div class="card bg-light-subtle border border-dashed border-gray-300 p-6 rounded-4 mb-8">
                        <div class="d-flex flex-column flex-sm-row align-items-center gap-6">
                            <div class="image-input image-input-outline image-input-empty position-relative" data-kt-image-input="true">
                                <div class="image-input-wrapper w-100px h-100px rounded-circle shadow-xs bg-body"></div>
                                <label class="btn btn-icon btn-circle btn-primary w-32px h-32px shadow position-absolute bottom-0 end-0 translate-middle-y" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Upload employee photo">
                                    <i class="ki-duotone ki-pencil fs-6 text-white"><span class="path1"></span><span class="path2"></span></i>
                                    <input type="file" name="employee_image" accept=".png, .jpg, .jpeg">
                                </label>
                            </div>
                            <div class="text-center text-sm-start">
                                <h4 class="text-gray-900 fw-bold fs-6 mb-1">Profile & Badge Headshot</h4>
                                <span class="text-muted fs-7 d-block mb-3">Upload a clean square photo (.png, .jpg, .jpeg). Maximum file size is 2MB.</span>
                                <div class="badge badge-light-primary fw-semibold fs-8">Used for ID Badges & Kiosk Profile</div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Credentials -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Badge ID / Employee Number</label>
                            <input type="text" name="badge_id" class="form-control form-control-sm" placeholder="e.g., BDG-9021">
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Kiosk PIN</label>
                            <input type="password" name="kiosk_pin" maxlength="6" class="form-control form-control-sm" placeholder="4-6 digit numeric pin">
                        </div>
                    </div>

                    <!-- Row 2: Company & Employment Type -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Company Branch / Entity</label>
                            <select name="company_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select company">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Employment Type</label>
                            <select name="employment_type_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select type">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Department & Job Position -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Department</label>
                            <select name="department_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select department">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Job Position</label>
                            <select name="job_position_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select position">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Manager & Status -->
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Direct Manager / Supervisor</label>
                            <select name="manager_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select manager">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Employment Status</label>
                            <select name="employment_status_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Select status">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Onboarding Date Only -->
                    <div class="row g-5">
                        <div class="col-12 col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Onboarding / Hire Date</label>
                            <input type="date" name="onboard_date" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <!--end::Step 2-->

            </div>
        </div>
        <!--end::Form Body Content-->

        <!--begin::Modern Sticky Footer Action Bar-->
        <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-5 px-lg-9 rounded-bottom-4 border-top border-gray-100 gap-3">
            <button type="button" class="btn btn-light btn-sm btn-active-color-primary fw-semibold px-6 w-100 w-md-auto">Cancel</button>
            <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                <button type="button" class="btn btn-light-primary btn-sm fw-semibold px-6 w-100 w-md-auto">Save as Draft</button>
                <button type="submit" class="btn btn-primary btn-sm fw-semibold px-8 shadow-sm w-100 w-md-auto">Save </button>
            </div>
        </div>
        <!--end::Footer Action Bar-->

    </div>
    <!--end::Modern Form Wrapper-->
</form>
@endsection