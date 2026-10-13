<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleWorkSchedulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('work_schedule_id') && is_string($this->work_schedule_id)) {
            $this->merge([
                'work_schedule_id' => array_map('intval', explode(',', $this->work_schedule_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'work_schedule_id'   => ['required', 'array', 'min:1'],
            'work_schedule_id.*' => ['integer', 'distinct', 'exists:work_schedules,id'],
        ];
    }
}
