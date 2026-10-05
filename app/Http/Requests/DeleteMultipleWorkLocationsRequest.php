<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleWorkLocationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('work_location_idid') && is_string($this->work_location_idid)) {
            $this->merge([
                'work_location_idid' => array_map('intval', explode(',', $this->work_location_idid)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'work_location_idid'   => ['required', 'array', 'min:1'],
            'work_location_idid.*' => ['integer', 'distinct', 'exists:work_locations,id'],
        ];
    }
}
