<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleJobPositionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('job_position_id') && is_string($this->job_position_id)) {
            $this->merge([
                'job_position_id' => array_map('intval', explode(',', $this->job_position_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'job_position_id'   => ['required', 'array', 'min:1'],
            'job_position_id.*' => ['integer', 'distinct', 'exists:job_positions,id'],
        ];
    }
}
