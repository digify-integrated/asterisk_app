<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteWorkScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'work_schedule_id' => ['required', 'integer', 'min:1', 'exists:work_schedules,id'],
        ];
    }
}
