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
        if ($this->has('work_location_id') && is_string($this->work_location_id)) {
            $this->merge([
                'work_location_id' => array_map('intval', explode(',', $this->work_location_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'work_location_id'   => ['required', 'array', 'min:1'],
            'work_location_id.*' => ['integer', 'distinct', 'exists:work_locations,id'],
        ];
    }
}
