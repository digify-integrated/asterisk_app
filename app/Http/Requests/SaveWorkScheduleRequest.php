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
            'work_schedule_id'   => ['nullable', 'integer', 'exists:work_schedules,id'],
            'name'              => ['required', 'string', 'max:100'],
        ];
    }
}
