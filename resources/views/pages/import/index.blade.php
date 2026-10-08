@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .dropzone-area {
            border: 2px dashed #cbd5e1;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .dropzone-area.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column gap-7 max-w-1000px mx-auto pb-10">
       
        {{-- CSV Import Card --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title fw-bold">Import CSV File</h3>
            </div>
            
            <form action="#" method="POST" enctype="multipart/form-data" id="csv-import-form">
                @csrf
                <div class="card-body">
                    
                    {{-- Drag and Drop Area --}}
                    <div id="drop-area" class="dropzone-area rounded-3 p-10 text-center bg-light">
                        <input type="file" name="csv_file" id="file-input" class="d-none" accept=".csv, text/csv">
                        
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            {{-- Icon --}}
                            <div class="symbol symbol-60px mb-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-file-up fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            
                            {{-- Text & File Info --}}
                            <div class="text-gray-700 fw-semibold fs-5 mb-1" id="drop-text">
                                Drag & Drop your CSV file here or <span class="text-primary fw-bold">browse</span>
                            </div>
                            <span class="text-muted fs-7">Only .csv files are supported (Max size: 10MB)</span>
                            
                            {{-- Selected File Display --}}
                            <div id="file-info" class="mt-4 fw-bold text-success d-none">
                                Selected: <span id="file-name"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                        Import File
                    </button>
                </div>
            </form>
        </div>
       
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('file-input');
            const fileInfo = document.getElementById('file-info');
            const fileNameDisplay = document.getElementById('file-name');
            const submitBtn = document.getElementById('submit-btn');

            // Trigger file input dialog when clicking drop area
            dropArea.addEventListener('click', () => fileInput.click());

            // Handle file selection via browse
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Highlight drop area when item is dragged over
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
            });

            // Handle dropped file
            dropArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files; // Assign files to hidden input
                    handleFile(files[0]);
                }
            });

            function handleFile(file) {
                if (file && (file.type === 'text/csv' || file.name.endsWith('.csv'))) {
                    fileNameDisplay.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    fileInfo.classList.remove('d-none');
                    submitBtn.removeAttribute('disabled');
                } else {
                    alert('Please select a valid CSV file.');
                    fileInput.value = ''; // Reset input
                    fileInfo.classList.add('d-none');
                    submitBtn.setAttribute('disabled', 'disabled');
                }
            }
        });
    </script>
@endpush