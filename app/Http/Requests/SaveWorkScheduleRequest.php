<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveWorkScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'work_schedule_id' => [
                'nullable',
                'integer',
                'exists:work_schedules,id',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'days' => [
                'required',
                'array:1,2,3,4,5,6,7',
                'size:7',
            ],

            'days.*' => [
                'required',
                'array',
            ],

            'days.*.is_rest_day' => [
                'sometimes',
                'boolean',
            ],

            'days.*.start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'days.*.end_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'days.*.break_start' => [
                'nullable',
                'date_format:H:i',
            ],

            'days.*.break_end' => [
                'nullable',
                'date_format:H:i',
            ],
        ];
    }
}
