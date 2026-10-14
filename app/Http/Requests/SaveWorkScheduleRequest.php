<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SaveWorkScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $days = $this->input('days', []);

        foreach ($days as $dayNum => $day) {
            $isRestDay = filter_var($day['is_rest_day'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $days[$dayNum]['is_rest_day'] = $isRestDay;

            // If it's a rest day, wipe out any time inputs
            if ($isRestDay) {
                $days[$dayNum]['start_time'] = null;
                $days[$dayNum]['end_time'] = null;
                $days[$dayNum]['break_start'] = null;
                $days[$dayNum]['break_end'] = null;
                continue;
            }

            // Convert 12-hour time formats to 24-hour (H:i)
            foreach (['start_time', 'end_time', 'break_start', 'break_end'] as $field) {
                if (!empty($day[$field])) {
                    try {
                        $days[$dayNum][$field] = Carbon::parse($day[$field])->format('H:i');
                    } catch (\Exception $e) {
                        // Leave as is if parsing fails
                    }
                } else {
                    $days[$dayNum][$field] = null;
                }
            }
        }

        $this->merge(['days' => $days]);
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
                'array',
                'size:7',
            ],
            'days.*.is_rest_day' => [
                'sometimes',
                'boolean',
            ],
            'days.*.start_time' => [
                'nullable',
                'required_if:days.*.is_rest_day,false',
                'date_format:H:i',
            ],
            'days.*.end_time' => [
                'nullable',
                'required_if:days.*.is_rest_day,false',
                'date_format:H:i',
                'after:days.*.start_time',
            ],
            'days.*.break_start' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:days.*.start_time',
                'before:days.*.end_time',
            ],
            'days.*.break_end' => [
                'nullable',
                'date_format:H:i',
                'after:days.*.break_start',
                'before_or_equal:days.*.end_time',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'days.*.start_time.required_if' => 'The start time is required when it is not a rest day.',
            'days.*.end_time.required_if' => 'The end time is required when it is not a rest day.',
            'days.*.end_time.after' => 'The end time must be later than the start time.',
            'days.*.break_start.after_or_equal' => 'The break start time must be during or after the shift start time.',
            'days.*.break_start.before' => 'The break start time must be before the shift end time.',
            'days.*.break_end.after' => 'The break end time must be later than the break start time.',
            'days.*.break_end.before_or_equal' => 'The break end time must be before or at the shift end time.',
        ];
    }
}