@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="d-flex flex-column gap-7 max-w-1000px mx-auto pb-10">
        <div class="card shadow-sm bg-light-primary border-primary border border-dashed">
            <div class="card-body p-6">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-information fs-2tx text-primary me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold fs-6 mb-1">Getting Started</h4>
                            <div class="fs-7 text-gray-700">Select your destination table and upload your properly formatted CSV file to begin mapping columns.</div>
                        </div>
                    </div>
                </div>

                <!-- Refined Collapsible Trigger & Drawer -->
                <div class="border-top border-primary border-opacity-20 mt-5 pt-4">
                    <button type="button" 
                            class="btn btn-active-light-primary btn-sm w-100 p-0 text-start d-flex align-items-center justify-content-between cursor-pointer border-0 bg-transparent" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#templateCollapse" 
                            aria-expanded="false" 
                            aria-controls="templateCollapse"
                            id="template-collapse-trigger">
                        <span class="text-gray-900 fw-semibold fs-7 d-flex align-items-center gap-2">
                            <span class="symbol symbol-25px symbol-circle">
                                <span class="symbol-label bg-primary bg-opacity-10 text-primary">
                                    <i class="ki-duotone ki-file-down fs-6"></i>
                                </span>
                            </span>
                            Need an import template? Browse available table schemas 
                            <i class="ki-duotone ki-arrow-right fs-5 text-gray-500"></i>
                        </span>
                        <span class="d-flex align-items-center text-gray-500 fs-8 fw-bold text-uppercase me-1 gap-2">
                            <span class="badge badge-light-primary fs-8 fw-semibold py-1 px-2">CSV Template</span>
                            <i class="ki-duotone ki-down fs-5 text-gray-500 transition-all transform-rotate"></i>
                        </span>
                    </button>

                    <div id="templateCollapse" class="collapse mt-3">
                        <div class="bg-body border border-gray-200 rounded-3 p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-gray-800 fw-bold fs-8 text-uppercase tracking-wider">Available Schema Downloads</span>
                                <span class="text-gray-500 fs-8">Includes all database fields excluding timestamps</span>
                            </div>
                            
                            <div id="dynamic-template-list" class="d-flex flex-column gap-2"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data" id="import_form">
            @csrf
            <div class="card shadow-sm mb-7" id="config-card">
                <div class="card-header">
                    <h3 class="card-title fw-bold">
                        <i class="ki-duotone ki-setting-2 fs-2 text-primary me-2"></i>
                        Step 1: Configuration & Upload
                    </h3>
                </div>
                <div class="card-body d-flex flex-column gap-7">
                    <div class="row g-7">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold required fs-6">Target Table</label>
                            <select name="database_table" id="database_table" class="form-select form-select-sm" data-control="select2" data-placeholder="Select target table..." data-allow-clear="true"></select>
                            <div class="form-text">Choose the database table where data will be imported.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold required fs-6">Import File</label>
                            <input type="file" name="import_file" id="import_file" class="form-control form-control-sm" accept=".csv, text/csv">
                            <div class="form-text">Max file size: 10MB. Only .csv formats are accepted.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-4">
                    <button type="button" class="btn btn-light-dark btn-sm px-6" id="configure-mapping">
                        <span class="indicator-label">Next: Configure Mapping</span>
                    </button>
                </div>
            </div>

            <div class="card shadow-sm d-none" id="mapping-card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title fw-bold m-0">
                        <i class="ki-duotone ki-columns fs-2 text-primary me-2"></i>
                        Step 2: Field Mapping & Data Preview
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" id="back-to-config-btn" class="btn btn-sm btn-light-dark">
                            <i class="ki-duotone ki-arrow-left fs-2 me-1"></i>
                            Change File / Table
                        </button>
                    </div>
                </div>

                <div class="card-body d-flex flex-column gap-5">
                    <span class="text-muted fs-7">Match your CSV header columns with the respective database attributes below.</span>

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

                <div class="card-footer d-flex justify-content-end gap-3 py-4">
                    <button type="button" class="btn btn-sm btn-danger" id="test-import-btn">
                        Test Import
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="submit-btn">
                        Commit Import
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection