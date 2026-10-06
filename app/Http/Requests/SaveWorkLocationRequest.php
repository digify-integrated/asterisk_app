<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveWorkLocationRequest extends FormRequest
{  
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'work_location_id'  => ['nullable', 'integer', 'exists:work_locations,id'],
            'name'              => ['required', 'string', 'max:255'],
            'location_type'     => ['required', 'string', 'max:255'],
            'street_1'          => ['nullable', 'string', 'max:255'],
            'street_2'          => ['nullable', 'string', 'max:255'],
            'barangay'          => ['nullable', 'string', 'max:255'],
            'city_id'           => ['nullable', 'integer', 'exists:cities,id'],
        ];
    }
}