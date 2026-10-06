<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveJobPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'job_position_id'   => ['nullable', 'integer', 'exists:job_positions,id'],
            'name'              => ['required', 'string', 'max:100'],
        ];
    }
}
