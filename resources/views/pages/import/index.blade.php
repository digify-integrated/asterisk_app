@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="d-flex flex-column gap-7 max-w-1000px mx-auto pb-10">

        <!-- Instructions Notice Card -->
        <div class="card shadow-sm bg-light-primary border-primary border border-dashed">
            <div class="card-body py-6">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold fs-6 mb-1">Getting Started</h4>
                            <div class="fs-7 text-gray-700">Select your destination table and upload your properly formatted CSV file to begin mapping columns. Need a reference? <a href="#" class="fw-bold text-primary">Download sample template</a>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Wrapper -->
        <form action="#" method="POST" enctype="multipart/form-data" id="universal-import-form">
            @csrf

            <!-- STEP 1 CARD: Target Table & File Selection -->
            <div class="card shadow-sm mb-7" id="config-card">
                <div class="card-header">
                    <h3 class="card-title fw-bold">
                        <i class="ki-duotone ki-setting-2 fs-2 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Step 1: Configuration & Upload
                    </h3>
                </div>
                <div class="card-body d-flex flex-column gap-7">
                    <div class="row g-7">
                        <!-- Target Table Selector -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold required fs-6">Target Table / Module</label>
                            <select name="target_table" id="target-table-select" class="form-select form-select-sm" data-control="select2" data-placeholder="Select target table...">
                                <option></option>
                                <option value="users">Users</option>
                                <option value="products">Products</option>
                                <option value="customers">Customers</option>
                            </select>
                            <div class="form-text">Choose the database table where data will be imported.</div>
                        </div>

                        <!-- File Input Section -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold required fs-6">Import File</label>
                            <input type="file" name="csv_file" id="file-input" class="form-control form-control-sm" accept=".csv, text/csv">
                            <div class="form-text">Max file size: 10MB. Only .csv formats are accepted.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-4">
                    <button type="button" class="btn btn-primary btn-sm px-6" id="proceed-btn">
                        <span class="indicator-label">Next: Configure Mapping</span>
                    </button>
                </div>
            </div>

            <!-- STEP 2 CARD: Odoo-Style Field Mapping & Preview Container (Initially Hidden) -->
            <div class="card shadow-sm d-none" id="mapping-card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title fw-bold m-0">
                        <i class="ki-duotone ki-columns fs-2 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Step 2: Field Mapping & Data Preview
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" id="back-to-config-btn" class="btn btn-sm btn-light-secondary">
                            <i class="ki-duotone ki-arrow-left fs-2 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Change File / Table
                        </button>
                    </div>
                </div>

                <div class="card-body d-flex flex-column gap-5">
                    <span class="text-muted fs-7">Match your CSV header columns with the respective database attributes below.</span>

                    <!-- Odoo-style Mapping Table -->
                    <div class="table-responsive border rounded">
                        <table class="table table-row-bordered table-row-gray-300 align-middle gs-4 gy-3 rounded shadow-sm m-0">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="min-w-200px ps-4">CSV Column Header</th>
                                    <th class="min-w-250px">Target Database Field</th>
                                    <th class="min-w-250px pe-4">Preview (Row 1 Sample)</th>
                                </tr>
                            </thead>
                            <tbody id="mapping-table-body">
                                <!-- Dynamic rows will be inserted here via your custom JS -->
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-gray-800">First Name</span>
                                        <span class="text-muted fs-8 d-block">Col #1</span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" data-control="select2">
                                            <option value="first_name" selected>first_name</option>
                                            <option value="name">name</option>
                                            <option value="ignore">— Do Not Import —</option>
                                        </select>
                                    </td>
                                    <td class="pe-4">
                                        <span class="text-gray-600 fs-7 bg-light px-2 py-1 rounded">John</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Actions for Mapping Step -->
                <div class="card-footer d-flex justify-content-end gap-3 py-4">
                    <button type="button" class="btn btn-light-danger btn-sm" id="test-import-btn">
                        Test Import
                    </button>
                    <button type="submit" class="btn btn-success btn-sm" id="submit-btn">
                        Commit Import
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('js')
    <!-- Left intentionally blank for your custom script implementation -->
@endpush