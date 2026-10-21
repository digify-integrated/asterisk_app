@extends('layouts.module')

@push('css')
    <link href="{{ asset('assets/plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body py-5 px-5">
            <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">                
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 grow">
                    @include('partials.datatable-search')
                </div>

                <div class="d-flex align-items-center justify-content-start flex-wrap gap-2">
                    @component('partials.datatable-actions')
                        @slot('deletePermission', $deletePermission)
                    @endcomponent  
                    
                    @component('partials.filter-button')
                        @slot('collapseId', 'work-schedule-filter-collapse')
                    @endcomponent

                    @if($exportPermission)
                        @include('partials.datatable-buttons')
                    @endif

                    @component('partials.column-dropdown')
                        @slot('dropdownId', 'work-schedule-table-column-dropdown')
                        @slot('dropdownButtonId', 'work-schedule-table-button-column-dropdown')
                    @endcomponent
                </div>
            </div>

            @component('partials.filter-module')
                @slot('collapseId', 'work-schedule-filter-collapse')
                @slot('resetFilterId', 'work-schedule-reset-filters-btn')
                @slot('applyFilterId', 'work-schedule-apply-filters-btn')
            @endcomponent
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-1 pb-0 pe-0 ps-0">
            @component('partials.index-table')
                @slot('tableId', 'work-schedule-table')
            @endcomponent
        </div>

        @component('partials.form-modal')
            @slot('formTitle', 'Work Schedule Details')
            @slot('formId', 'work_schedule_form')
            @slot('size', 'xl')

            <input type="hidden" id="work_schedule_id" name="work_schedule_id" />

            @php
                $daysOfWeek = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday',
                ];
            @endphp

            <div class="d-flex flex-column gap-5">
                <div class="d-flex flex-column gap-2">
                    <label class="form-label fw-semibold text-dark required fs-7 mb-0" for="name">Schedule Name</label>
                    <input type="text" class="form-control form-control-sm w-100" id="name" name="name" placeholder="Enter Schedule Name" maxlength="100" autocomplete="off">
                </div>

                <div class="border-top my-1"></div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-semibold text-dark fs-7 mb-0">Schedule</label>
                        <span class="text-muted fs-8">Configure hours or mark as rest days</span>
                    </div>
                    
                    <div class="card border overflow-hidden">
                        <div class="table-responsive mb-0">
                            <table class="table table-borderless align-middle text-center mb-0">
                                <thead>
                                    <tr class="border-bottom text-uppercase fs-8 text-muted fw-bold tracking-wider">
                                        <th class="text-start ps-4 py-3" style="width: 130px;"></th>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <th class="py-3 px-2">{{ $dayName }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="fs-7">
                                    <tr class="border-bottom border-light">
                                        <td class="text-start ps-4 text-muted fw-medium py-3">Rest Day</td>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <td class="py-3 px-2">
                                                <div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-center">
                                                    <input class="form-check-input rest-day-toggle h-20px w-30px" type="checkbox" role="switch" 
                                                        id="is_rest_day_{{ $dayNum }}" 
                                                        name="days[{{ $dayNum }}][is_rest_day]" 
                                                        value="1" data-day="{{ $dayNum }}">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="border-bottom border-light">
                                        <td class="text-start ps-4 text-muted fw-medium py-2">Start Time</td>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <td class="py-2 px-2">
                                                <input type="time" class="form-control form-control-sm time-input" 
                                                    id="start_time_{{ $dayNum }}" 
                                                    name="days[{{ $dayNum }}][start_time]">
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="border-bottom border-light">
                                        <td class="text-start ps-4 text-muted fw-medium py-2">End Time</td>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <td class="py-2 px-2">
                                                <input type="time" class="form-control form-control-sm time-input" 
                                                    id="end_time_{{ $dayNum }}" 
                                                    name="days[{{ $dayNum }}][end_time]">
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="border-bottom border-light">
                                        <td class="text-start ps-4 text-muted fw-medium py-2">Break Start</td>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <td class="py-2 px-2">
                                                <input type="time" class="form-control form-control-sm time-input" 
                                                    id="break_start_{{ $dayNum }}" 
                                                    name="days[{{ $dayNum }}][break_start]">
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td class="text-start ps-4 text-muted fw-medium py-3">Break End</td>
                                        @foreach($daysOfWeek as $dayNum =>$dayName)
                                            <td class="py-3 px-2">
                                                <input type="time" class="form-control form-control-sm time-input" 
                                                    id="break_end_{{ $dayNum }}" 
                                                    name="days[{{ $dayNum }}][break_end]">
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>

    @include('partials.log-notes-modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/datatables.bundle.js') }}"></script>
@endpush

